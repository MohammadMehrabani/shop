<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Database\Eloquent\Model;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected Model $user;
    protected string $apiToken;

    protected function setUp(): void
    {
        parent::setUp();
        list($user, $apiToken) = $this->getUserToken();
        $this->user = $user;
        $this->apiToken = $apiToken;
    }

    protected function getUserToken(array $attributes = []): array
    {
        $user = User::factory()->create($attributes);
        $token = $this->json('post', '/api/user/login', [
            'mobile' => $user->mobile,
            'password' => 'password'
        ]);
        $token = json_decode($token->getContent())->data->access_token;
        return [$user, $token];
    }
}
