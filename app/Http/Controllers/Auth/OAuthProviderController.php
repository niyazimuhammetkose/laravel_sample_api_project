<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\OAuthProviderEnum;
use App\Models\OAuthProvider;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class OAuthProviderController extends Controller
{

    public function index($provider)
    {
        // Validate and convert provider to enum
        if (!in_array($provider, OAuthProviderEnum::values())) {
            Log::error('Invalid OAuth provider: ' . $provider);
            return redirect(config('app.frontend_url') . '/login')->with('error', 'Unsupported OAuth provider');
        }

        $providerEnum = OAuthProviderEnum::from($provider);

        try {
            return Socialite::driver($providerEnum->value)->redirect();
        } catch (Exception $e) {
            Log::error('OAuth redirection failed: ' . $e->getMessage());
            return redirect(config('app.frontend_url') . '/login')->with('error', 'OAuth redirection failed');
        }
    }

    public function store(OAuthProviderEnum $provider)
    {
        try {
            $socialiteUser = Socialite::driver($provider->value)->user();

            // Validate and convert provider to enum
            if (is_null($socialiteUser->getEmail())) {
                Log::error('Incomplete data from OAuth provider: ' . $provider->value);
                return redirect(config('app.frontend_url') . '/login')->with('error', 'Incomplete data from OAuth provider');
            }

            $user = User::firstOrCreate([
                'email' => $socialiteUser->getEmail(),
            ], [
                'name' => $socialiteUser->getName(),
            ]);

            $user->OAuthProviders()->updateOrCreate([
                'provider' => $provider,
                'provider_id' => $socialiteUser->getId(),
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect(config('app.frontend_url') . '/dashboard');
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('OAuth failed: ' . $e->getMessage());

            // Redirect or return a response with an error message
            return redirect(config('app.frontend_url') . '/login')->with('error', 'OAuth failed');
        }
    }
}
