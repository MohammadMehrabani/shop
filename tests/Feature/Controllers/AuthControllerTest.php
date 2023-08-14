<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_not_access_user_info_without_api_token(): void
    {
        $this->json('get','/api/user/me')
            ->assertExactJson(['success' => false, "errors" => "Authorization Token not found"]);
    }

    public function test_not_access_user_info_with_expire_api_token(): void
    {
        // in the jwt.php file, ttl is set to 60 minutes by default
        $this->travel(5)->hours();

        $this->withHeader('Authorization', 'Bearer '.$this->apiToken)
            ->json('get','/api/user/me')
            ->assertStatus(401)
            ->assertExactJson(['success' => false, "errors" => "Token is Expired"]);
    }

    public function test_not_access_user_info_with_invalid_api_token(): void
    {
        $this->withHeader('Authorization', 'Bearer test')
            ->json('get','/api/user/me')
            ->assertStatus(401)
            ->assertExactJson(['success' => false, "errors" => "Token is Invalid"]);
    }

    public function test_access_user_info_with_api_token(): void
    {
        $this->withHeader('Authorization', 'Bearer '.$this->apiToken)
            ->json('get','/api/user/me')
            ->assertStatus(200)
            ->assertJsonPath('data.mobile', $this->user->mobile);
    }

    public function test_unverified_user_does_not_have_the_ability_to_log_in(): void
    {
        $user = User::factory()->create(['mobile_verified_at' => null, 'password' => null]);
        $this->json('post','/api/user/login',[
                'mobile' => $user->mobile,
                'password' => 'password'
            ])
            ->assertStatus(401)
            ->assertExactJson(['success' => false, "errors" => "Unauthenticated."]);

        $this->json('post','/api/user/login',[
            'mobile' => '09188888888',
            'password' => 'password'
        ])
            ->assertStatus(401)
            ->assertExactJson(['success' => false, "errors" => "Unauthenticated."]);
    }

    public function test_successful_log_in(): void
    {
        $this->json('post','/api/user/login',[
                'mobile' => $this->user->mobile,
                'password' => 'password'
            ])
            ->assertStatus(200)
            ->assertJsonPath("data.token_type","bearer");
    }

    public function test_a_new_user_created_and_will_be_redirected_to_verify_page(): void
    {
        $mobile = '09366666666';

        $this->json('post','/api/user/authenticate',[
                'mobile' => $mobile
            ])
            ->assertStatus(200)
            ->assertExactJson([
                'success' => true,
                "data.mobile" => $mobile,
                "data.newUser" => true,
                "data.nextPage" => 'verifyOtp',
            ]);
    }

    public function test_user_redirected_to_login_page(): void
    {
        $this->json('post','/api/user/authenticate',[
                'mobile' => $this->user->mobile
            ])
            ->assertStatus(200)
            ->assertExactJson([
                'success' => true,
                "data.mobile" => $this->user->mobile,
                "data.newUser" => false,
                "data.nextPage" => 'login',
            ]);
    }

    public function test_get_validation_error_when_send_invalid_mobile_for_authenticate(): void
    {
        $this->json('post','/api/user/authenticate',[
                'mobile' => ''
            ])
            ->assertStatus(422)
            ->assertExactJson([
                'success' => false,
                "errors.mobile" => 'The mobile field is required.',
            ]);

        $this->json('post','/api/user/authenticate',[
                'mobile' => '06966666666'
            ])
            ->assertStatus(422)
            ->assertExactJson([
                'success' => false,
                "errors.mobile" => 'the mobile format is invalid.',
            ]);

        $this->json('post','/api/user/authenticate',[
                'mobile' => '0696666666'
            ])
            ->assertStatus(422)
            ->assertExactJson([
                'success' => false,
                "errors.mobile" => 'The mobile field must be 11 digits.',
            ]);
    }

    public function test_successful_sent_otp(): void
    {
        $response = $this->json('post','/api/user/send/otp',[
                'mobile' => $this->user->mobile
            ])
            ->assertStatus(200);

        $response = json_decode($response->content());
        $this->assertStringContainsString('otp sent successfully', $response->data);
    }

    public function test_not_send_otp_when_previous_otp_has_not_yet_expired(): void
    {
        $this->test_successful_sent_otp();

        $this->json('post','/api/user/send/otp',[
                'mobile' => $this->user->mobile
            ])
            ->assertStatus(400)
            ->assertExactJson([
                'success' => false,
                "errors" => 'your previous otp has not yet expired.',
            ]);
    }

    public function test_get_validation_error_when_send_invalid_mobile_for_sendOtp(): void
    {
        $this->json('post','/api/user/send/otp',[
            'mobile' => ''
        ])
            ->assertStatus(422)
            ->assertExactJson([
                'success' => false,
                "errors.mobile" => 'The mobile field is required.',
            ]);

        $this->json('post','/api/user/send/otp',[
                'mobile' => '06966666666'
            ])
            ->assertStatus(422)
            ->assertExactJson([
                'success' => false,
                "errors.mobile" => 'the mobile format is invalid.',
            ]);

        $this->json('post','/api/user/send/otp',[
                'mobile' => '0696666666'
            ])
            ->assertStatus(422)
            ->assertExactJson([
                'success' => false,
                "errors.mobile" => 'The mobile field must be 11 digits.',
            ]);
    }

    public function test_successful_verify_otp_and_will_be_redirected_to_register_page(): void
    {
        $user = User::factory()->create(['mobile_verified_at' => null, 'password' => null]);

        $response = $this->json('post','/api/user/send/otp',[
                'mobile' => $user->mobile
            ])
            ->assertStatus(200);
        $response = json_decode($response->content());
        $this->assertStringContainsString('otp sent successfully', $response->data);

        $otp = Cache::has('otp_'.$user->mobile)
                ? Cache::get('otp_'.$user->mobile)
                : '';

        $this->json('post','/api/user/verify/otp',[
                'mobile' => $user->mobile,
                'code' => $otp
            ])
            ->assertStatus(200)
            ->assertExactJson([
                'success' => true,
                "data.mobile" => $user->mobile,
                "data.newUser" => true,
                "data.nextPage" => 'register',
            ]);
    }

    public function test_get_validation_error_when_send_invalid_otp_for_verifyOtp(): void
    {
        $this->json('post','/api/user/verify/otp',[
                'mobile' => $this->user->mobile,
                'code' => '01235'
            ])
            ->assertStatus(422)
            ->assertExactJson([
                'success' => false,
                "errors.code" => 'otp is invalid.',
            ]);

        $this->json('post','/api/user/verify/otp',[
                'mobile' => $this->user->mobile,
                'code' => ''
            ])
            ->assertStatus(422)
            ->assertExactJson([
                'success' => false,
                "errors.code" => 'The code field is required.',
            ]);

        $this->json('post','/api/user/verify/otp',[
                'mobile' => $this->user->mobile,
                'code' => '0123'
            ])
            ->assertStatus(422)
            ->assertExactJson([
                'success' => false,
                "errors.code" => 'The code field must be 5 digits.',
            ]);

        $this->test_successful_sent_otp();
        $otp = Cache::has('otp_'.$this->user->mobile)
            ? Cache::get('otp_'.$this->user->mobile)
            : '';
        $this->json('post','/api/user/verify/otp',[
                'mobile' => $this->user->mobile,
                'code' => $otp
            ])
            ->assertStatus(400)
            ->assertExactJson([
                'success' => false,
                "errors" => 'incorrect request',
            ]);
    }

    public function test_successful_register(): void
    {
        $user = User::factory()->create(['password' => null]);

        $this->json('post','/api/user/register',[
                'mobile' => $user->mobile,
                'password' => 'secret',
                'password_confirmation' => 'secret',
                'firstname' => 'mohammad hossein',
                'lastname' => 'saed mehrabani',
            ])
            ->assertStatus(200)
            ->assertJsonPath("data.token_type","bearer");
    }

    public function test_get_validation_error_when_send_invalid_request_for_register(): void
    {
        $user = User::factory()->create(['password' => null]);

        $this->json('post','/api/user/register',[
                'mobile' => $user->mobile,
                'password' => 'secret',
                'password_confirmation' => 'secret',
                'firstname' => 'mohammad hossein',
                'lastname' => '',
            ])
            ->assertStatus(422)
            ->assertExactJson([
                'success' => false,
                "errors.lastname" => 'The lastname field is required.',
            ]);

        $this->json('post','/api/user/register',[
                'mobile' => $this->user->mobile,
                'password' => 'secret',
                'password_confirmation' => 'secret',
                'firstname' => 'mohammad hossein',
                'lastname' => 'saed mehrabani',
            ])
            ->assertStatus(400)
            ->assertExactJson([
                'success' => false,
                "errors" => 'incorrect request',
            ]);
    }

    public function test_successful_refresh_token(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->apiToken)
            ->json('post','/api/user/refresh')
            ->assertStatus(200)
            ->assertJsonPath("data.token_type","bearer");

        $newToken = json_decode($response->content())->data->access_token;

        $this->withHeader('Authorization', 'Bearer '.$this->apiToken)
            ->json('get','/api/user/me')
            ->assertStatus(401)
            ->assertJsonPath("errors","Token is Invalid");

        $this->withHeader('Authorization', 'Bearer '.$newToken)
            ->json('get','/api/user/me')
            ->assertStatus(200)
            ->assertJsonPath("data.mobile",$this->user->mobile);
    }

    public function test_refresh_token_with_active_blacklist(): void
    {
        JWTAuth::manager()->setBlacklistEnabled(true); // config('jwt.blacklist_enabled')

        $this->withHeader('Authorization', 'Bearer '.$this->apiToken)
            ->json('post','/api/user/refresh')
            ->assertStatus(200)
            ->assertJsonPath("data.token_type","bearer");

        $this->withHeader('Authorization', 'Bearer '.$this->apiToken)
            ->json('post','/api/user/refresh')
            ->assertStatus(400)
            ->assertJsonPath("errors","The token has been blacklisted");
    }

    public function test_refresh_token_with_inactive_blacklist(): void
    {
        JWTAuth::manager()->setBlacklistEnabled(false); // config('jwt.blacklist_enabled')

        $this->withHeader('Authorization', 'Bearer '.$this->apiToken)
            ->json('post','/api/user/refresh')
            ->assertStatus(200)
            ->assertJsonPath("data.token_type","bearer");

        $this->withHeader('Authorization', 'Bearer '.$this->apiToken)
            ->json('post','/api/user/refresh')
            ->assertStatus(200)
            ->assertJsonPath("data.token_type","bearer");
    }

    public function test_successful_log_out(): void
    {
        $this->withHeader('Authorization', 'Bearer '.$this->apiToken)
            ->json('post','/api/user/logout')
            ->assertStatus(200)
            ->assertJsonPath("data","Successfully logged out");

        $this->withHeader('Authorization', 'Bearer '.$this->apiToken)
            ->json('get','/api/user/me')
            ->assertStatus(401)
            ->assertJsonPath("errors","Token is Invalid");
    }
}
