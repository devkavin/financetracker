<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    use RefreshDatabase;

    public function test_user_can_request_verification_email(): void
    {
        $user = \App\Models\User::factory()->unverified()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        \Mail::fake();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/v1/email/verification-notification');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Verification email sent.',
            ]);
    }

    public function test_user_can_verify_email(): void
    {
        $user = \App\Models\User::factory()->unverified()->create();
        $verificationUrl = \URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($verificationUrl);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Email verified successfully.',
            ]);
    }

    public function test_guest_cannot_request_verification_email(): void
    {
        $response = $this->postJson('/v1/email/verification-notification');
        $response->assertStatus(401);
    }
}
