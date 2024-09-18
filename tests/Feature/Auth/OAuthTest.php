<?php

namespace Tests\Feature\Auth;

use App\Enums\OAuthProviderEnum;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class OAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public static function providerData(): array
    {
        return [
            [OAuthProviderEnum::GOOGLE],
            [OAuthProviderEnum::FACEBOOK],
            [OAuthProviderEnum::GITHUB],
            [OAuthProviderEnum::LINKEDIN_OPENID],
            [OAuthProviderEnum::X],
            [OAuthProviderEnum::APPLE],
        ];
    }

    #[TestDox('User is redirected to the correct provider link for $providerEnum')]
    #[DataProvider('providerData')]
    public function test_user_redirects_to_provider_link(OAuthProviderEnum $providerEnum)
    {
        $this->assertUserRedirectToProviderLink($providerEnum);
    }

    #[TestDox('User is redirected to frontend login page with an error message when $providerEnum redirection fails')]
    #[DataProvider('providerData')]
    public function test_oauth_redirection_fails_gracefully(OAuthProviderEnum $providerEnum)
    {
        $this->assertProviderRedirectionFailsGracefully($providerEnum);
    }

    #[TestDox('User is created and logs in successfully with $providerEnum provider')]
    #[DataProvider('providerData')]
    public function test_provider_callback_creates_and_logs_in_user(OAuthProviderEnum $providerEnum)
    {
        $this->assertProvideCallbackCreatesAndLogsInUser($providerEnum);
    }

    #[TestDox('User is redirected to the frontend login page with an error message when $providerEnum provider callback fails')]
    #[DataProvider('providerData')]
    public function test_provider_callback_fails_gracefully(OAuthProviderEnum $providerEnum)
    {
        $this->assertProviderCallbackFailsGracefully($providerEnum);
    }

    #[TestDox('User is redirected to the frontend login page with an error message when $providerEnum provider returns incomplete data')]
    #[DataProvider('providerData')]
    public function test_oauth_provider_returns_incomplete_data(OAuthProviderEnum $providerEnum)
    {
        $this->assertProviderReturnsIncompleteDataFailsGracefully($providerEnum);
    }

    #[TestDox('User is redirected to the frontend login page with an error message when an unsupported OAuth provider is requested')]
    public function test_unsupported_oauth_provider_fails_gracefully()
    {
        $unsupportedProvider = 'unsupported-provider';

        $this->assertUnsupportedProviderFailsGracefully($unsupportedProvider);
    }

    private function assertUserRedirectToProviderLink(OAuthProviderEnum $providerEnum): void
    {
        $providerRedirectLink = config('services.' . $providerEnum->value . 'redirect');

        // Mock Socialite to return a redirect response
        Socialite::shouldReceive('driver')
            ->with($providerEnum->value)
            ->once()
            ->andReturn(Mockery::mock(['redirect' => response()->redirectTo($providerRedirectLink)]));

        // Act
        $response = $this->get(route('login.provider', $providerEnum->value));

        // Assert
        $response->assertRedirect($providerRedirectLink);
    }

    private function assertProviderRedirectionFailsGracefully(OAuthProviderEnum $providerEnum): void
    {
        // Mock Socialite to throw an exception
        Socialite::shouldReceive('driver')
            ->with($providerEnum->value)
            ->once()
            ->andThrow(new Exception('OAuth redirection failed'));

        // Act
        $response = $this->get(route('login.provider', $providerEnum->value));

        // Assert
        $response->assertStatus(302); // Assert that it redirects
        $response->assertRedirect(config('app.frontend_url') . '/login'); // Assert correct redirection
        $response->assertSessionHas('error', 'OAuth redirection failed'); // Assert session contains the error message
    }

    private function assertProvideCallbackCreatesAndLogsInUser(OAuthProviderEnum $providerEnum): void
    {
        $mockSocialiteUser = Mockery::mock();
        $mockSocialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $mockSocialiteUser->shouldReceive('getName')->andReturn('Test User');
        $mockSocialiteUser->shouldReceive('getId')->andReturn('12345');

        Socialite::shouldReceive('driver')
            ->with($providerEnum->value)
            ->once()
            ->andReturn(Mockery::mock(['user' => $mockSocialiteUser]));

        // Act
        $response = $this->get(route('login.provider.callback', $providerEnum->value));

        // Assert user is created
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $user = User::where('email', 'test@example.com')->first();

        // Assert user is logged in
        $this->assertTrue(Auth::check());
        $this->assertEquals(Auth::user()->id, $user->id);

        // Assert redirection
        $response->assertRedirect(config('app.frontend_url') . '/dashboard');
    }

    private function assertProviderCallbackFailsGracefully(OAuthProviderEnum $providerEnum): void
    {
        // Mock Socialite to throw an exception
        Socialite::shouldReceive('driver')
            ->with($providerEnum->value)
            ->once()
            ->andThrow(new Exception('OAuth failed'));

        // Act
        $response = $this->get(route('login.provider.callback', $providerEnum->value));

        // Assert that it redirects to the login page
        $response->assertStatus(302);
        $response->assertRedirect(config('app.frontend_url') . '/login');

        // Assert that the session contains the error message
        $response->assertSessionHas('error', 'OAuth failed');
    }

    private function assertProviderReturnsIncompleteDataFailsGracefully(OAuthProviderEnum $providerEnum): void
    {
        $mockSocialiteUser = Mockery::mock();
        $mockSocialiteUser->shouldReceive('getEmail')->andReturn(null); // Missing email

        Socialite::shouldReceive('driver')
            ->with($providerEnum->value)
            ->once()
            ->andReturn(Mockery::mock(['user' => $mockSocialiteUser]));

        // Act
        $response = $this->get(route('login.provider.callback', $providerEnum->value));

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect(config('app.frontend_url') . '/login');
        $response->assertSessionHas('error', 'Incomplete data from OAuth provider');
    }

    private function assertUnsupportedProviderFailsGracefully($provider): void
    {
        // Act
        $response = $this->get(route('login.provider', $provider));

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect(config('app.frontend_url') . '/login');
        $response->assertSessionHas('error', 'Unsupported OAuth provider');
    }

}
