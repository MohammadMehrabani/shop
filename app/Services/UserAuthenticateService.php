<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use App\Contracts\UserAuthenticateServiceInterface;
use App\DataTransferObjects\UserDto;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserAuthenticateService implements UserAuthenticateServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function authenticate(UserDto $userDto)
    {
        $user = $this->userRepository->findByMobile($userDto->mobile);

        if ($user) {

            if ($user->password && $user->mobile_verified_at) {
                return [
                    'mobile' => $user->mobile,
                    'newUser' => false,
                    'nextPage' => 'login'
                ];
            }

            return [
                'mobile' => $user->mobile,
                'newUser' => true,
                'nextPage' => 'verifyOtp'
            ];

        }

        $newUser = $this->userRepository->create($userDto);

        return [
            'mobile' => $newUser->mobile,
            'newUser' => true,
            'nextPage' => 'verifyOtp'
        ];
    }

    public function sendOtp(UserDto $userDto)
    {
        $user = $this->userRepository->findByMobile($userDto->mobile);

        if ($user && !Cache::has('otp_'.$userDto->mobile)) {

            $code = rand(10000, 99999);

            // If there was an SMS panel
            // e.g. send otp with sms using queue (job)
            //if (env('APP_ENV') == 'production')
            //SendLoginCodeJob::dispatch($arguments->mobile, $code)->onQueue('otp');

            return Cache::add('otp_'.$userDto->mobile, $code, now()->addMinutes(2));

        } elseif($user && Cache::has('otp_'.$userDto->mobile)) {
            throw new ApiException('your previous otp has not yet expired.', 400);
        } else {
            throw new ApiException('user not found.', 404);
        }
    }

    public function verifyOtp(UserDto $userDto)
    {
        $user = $this->userRepository->findByMobile($userDto->mobile);

        if ($user && Cache::has('otp_'.$userDto->mobile) &&
            Cache::get('otp_'.$userDto->mobile) == $userDto->otp
        ) {
            if (!$user->mobile_verified_at && !$user->password) {

                $this->userRepository->update(
                    $user,
                    UserDto::fromArray(['mobileVerifiedAt' => now()->format('Y-m-d H:i:s')])
                );

                return [
                    'mobile' => $userDto->mobile,
                    'newUser' => true,
                    'nextPage' => 'register'
                ];
            }

            throw new ApiException('incorrect request', 400);
        }

        throw ValidationException::withMessages(['code' => 'otp is invalid.']);
    }

    public function register(UserDto $userDto)
    {
        $user = $this->userRepository->findByMobile($userDto->mobile);

        if ($user && $user->mobile_verified_at && !$user->password) {

            $this->userRepository->update($user, $userDto);

            $token = auth()->attempt([
                'mobile' => $userDto->mobile, 'password' => $userDto->password
            ]);

            if (! $token) {
                throw new AuthenticationException();
            }

            return $this->respondWithToken($token);
        }

        throw new ApiException('incorrect request', 400);
    }

    public function login(UserDto $userDto)
    {
        if (! $token = auth()->attempt(['mobile' => $userDto->mobile, 'password' => $userDto->password])) {
            throw new AuthenticationException();
        }

        return $this->respondWithToken($token);
    }

    public function refresh()
    {
        try {
            return $this->respondWithToken(auth()->refresh());
        } catch (JWTException $e) {
            throw new ApiException($e->getMessage(), 400);
        }
    }

    private function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
