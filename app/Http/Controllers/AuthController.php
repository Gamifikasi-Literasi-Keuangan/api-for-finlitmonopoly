<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PlayerService;

class AuthController extends Controller
{
    protected $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    /*
     * Initial State: 
     * Final State: 
     */
    public function google(Request $request)
    {
        $data = $request->validate([
            'google_id_token' => 'required|string',
            'platform' => 'nullable|string',
            'locale' => 'nullable|string'
        ]);

        try {
            $result = $this->playerService->loginWithGoogle($data);

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication Failed',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Refresh Token
     * POST /auth/refresh
     */
    public function refresh(Request $request)
    {
        $data = $request->validate([
            'refresh_token' => 'required|string'
        ]);

        try {
            $result = $this->playerService->refreshToken($data['refresh_token']);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid or Expired Refresh Token'
            ], 401);
        }
    }
    /**
     * Web OAuth Flow: Start
     * GET /auth/google/web/start
     */
    public function googleWebStart(Request $request)
    {
        $state = Str::random(40);
        $locale = $request->query('locale', 'id_ID');

        // Simpan State & Locale sementara di Cache/Session
        // Expiry 5 menit
        \Illuminate\Support\Facades\Cache::put('oauth_state_' . $state, ['locale' => $locale], 300);

        return \Laravel\Socialite\Facades\Socialite::driver('google')
            ->with(['state' => $state])
            ->stateless()
            ->redirect();
    }

    /**
     * Web OAuth Flow: Callback
     * GET /auth/google/web/callback
     */
    public function googleWebCallback(Request $request)
    {
        $state = $request->query('state');
        $cacheKey = 'oauth_state_' . $state;

        $validateParams = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (!$state || !$validateParams) {
            return response()->json(['error' => 'Invalid State or Timeout'], 400);
        }

        try {
            $socialiteUser = \Laravel\Socialite\Facades\Socialite::driver('google')->stateless()->user();

            // Proses Login via PlayerService
            $locale = $validateParams['locale'] ?? 'id_ID';
            $result = $this->playerService->handleSocialiteCallback($socialiteUser, 'web', $locale);

            // Simpan Hasil Login di Cache agar bisa diambil oleh Frontend
            \Illuminate\Support\Facades\Cache::put('oauth_result_' . $state, $result, 300);

            // Hapus State lama
            \Illuminate\Support\Facades\Cache::forget($cacheKey);

            // Tampilkan Halaman Sukses Sederhana (atau tutup window otomatis)
            return response("Login Success! You can close this window.", 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Cache::put('oauth_result_' . $state, ['error' => 'Login Failed', 'message' => $e->getMessage()], 300);
            return response("Login Failed: " . $e->getMessage(), 400);
        }
    }

    /**
     * Web OAuth Flow: Result
     * GET /auth/google/web/result
     */
    public function googleWebResult(Request $request)
    {
        $state = $request->query('state');
        if (!$state)
            return response()->json(['error' => 'State required'], 400);

        $result = \Illuminate\Support\Facades\Cache::get('oauth_result_' . $state);

        if (!$result) {
            return response()->json(['status' => 'pending'], 200);
        }

        // Jika error tersimpan
        if (isset($result['error'])) {
            return response()->json(['status' => 'error', 'error' => $result['error']], 400);
        }

        return response()->json(array_merge(['status' => 'ok'], $result), 200);
    }
}
