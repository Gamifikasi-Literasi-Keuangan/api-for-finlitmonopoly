<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle a login request by verifying credentials against the API (/auth/login).
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Determine API URL. If AUTH_API_URL is set in .env, use it; otherwise use local /auth/login
        $apiUrl = env('AUTH_API_URL', url('/auth/login'));

        try {
            $response = Http::asForm()->post($apiUrl, [
                'email' => $data['email'],
                'password' => $data['password'],
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Could not contact authentication server.'])->withInput();
        }

        if (!$response->successful()) {
            // Try to extract message
            $message = $response->json('message') ?: 'Invalid credentials.';
            return back()->withErrors(['email' => $message])->withInput();
        }

        $respJson = $response->json();

        // Expected response: contains user data and token. We'll handle multiple common keys.
        $token = $respJson['token'] ?? $respJson['access_token'] ?? null;
        $apiUser = $respJson['user'] ?? ($respJson['data'] ?? null);

        if (!$apiUser || !isset($apiUser['email'])) {
            // If API returns only success without user, fallback to logged-in by email
            $apiUser = ['email' => $data['email'], 'name' => $apiUser['name'] ?? $apiUser['email'] ?? $data['email']];
        }

        // Find or create local user record.
        $user = User::updateOrCreate(
            ['email' => $apiUser['email']],
            ['name' => $apiUser['name'] ?? $apiUser['email'], 'password' => bcrypt(Str::random(40))]
        );

        // Log the user into the application (session-based)
        Auth::login($user);

        // Store API token in session for later use when calling API on behalf of admin
        if ($token) {
            session(['api_token' => $token]);
        }

        return redirect()->intended('/');
    }
}
