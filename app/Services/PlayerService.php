<?php

namespace App\Services;

use App\Models\User;
use App\Models\Player;
use App\Models\PlayerProfile;
use App\Models\AuthToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Google_Client;

class PlayerService
{
    /**
     * Menangani Login dengan Google
     * Sesuai API Versi 3: POST /auth/google
     */
    public function loginWithGoogle(array $data)
    {
        $idToken = $data['google_id_token'];
        $platform = $data['platform'] ?? 'web';
        $locale = $data['locale'] ?? 'id_ID';

        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        try {
            $payload = $client->verifyIdToken($idToken);
        } catch (\Exception $e) {
            $payload = false;
        }

        if (!$payload) {
            throw new \Exception("Invalid Google Token (Wrong format or expired)");
        }
        $googleId = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'];
        $avatar = $payload['picture'] ?? null;


        return DB::transaction(function () use ($googleId, $name, $avatar, $platform, $locale) {
            // 1. Cari atau Buat User
            $user = User::where('google_id', $googleId)->first();
            $isNewUser = false;

            if (!$user) {
                // New User
                $user = User::create([
                    'google_id' => $googleId,
                    'username' => $name,
                    'role' => 'player',
                    'avatar' => $avatar,
                    'passwordHash' => null
                ]);
                $isNewUser = true;
            } else {
                // Existing User - Sync Data
                $user->update([
                    'username' => $name,
                    'avatar' => $avatar
                ]);
            }

            // 2. Cari atau Buat Player
            $player = Player::where('user_id', $user->id)->first();

            if (!$player) {
                $player = Player::create([
                    'PlayerId' => 'player_' . Str::random(8),
                    'user_id' => $user->id, // Establish Relation
                    'name' => $name,
                    'avatar_url' => $avatar,
                    'initial_platform' => $platform,
                    'locale' => $locale,
                    'gamesPlayed' => 0,
                    'created_at' => now()
                ]);

                // Create Profile for new Player
                PlayerProfile::create([
                    'PlayerId' => $player->PlayerId,
                    'cluster' => null,
                    'confidence_level' => 0.0,
                    'lifetime_scores' => json_encode([]),
                    'thresholds' => json_encode(["critical" => 0.30, "high" => 0.50, "medium" => 0.70]),
                    'last_updated' => now(),
                ]);
            } else {
                // Existing Player - Sync Data
                $player->update([
                    'name' => $name,
                    'avatar_url' => $avatar,
                    'locale' => $locale
                    // 'initial_platform' tidak diupdate karena historis
                ]);
            }

            return $this->generateTokens($user, $player, $isNewUser);
        });
    }

    /**
     * Logika Generate Token Lengkap (Access + Refresh)
     * Update fungsi loginWithGoogle Anda untuk memanggil ini
     */
    private function generateTokens($user, $player, $isNewUser = false)
    {
        $accessToken = $user->createToken('game-client')->plainTextToken;
        $refreshTokenString = Str::random(60);

        DB::table('auth_tokens')->insert([
            'token' => $refreshTokenString,
            'type' => 'refresh',
            'userId' => $user->id,
            'expiresAt' => now()->addDays(30),
            'created_at' => now()
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshTokenString,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'player_id' => $player->PlayerId,
            'username' => $player->name,
            'auth_status' => 'ok',
            'is_new_user' => $isNewUser
        ];
    }

    /**
     * Endpoint: POST /auth/refresh
     */
    public function refreshToken($refreshTokenInput)
    {
        $tokenRecord = DB::table('auth_tokens')
            ->where('token', $refreshTokenInput)
            ->where('type', 'refresh')
            ->first();

        if (!$tokenRecord) {
            throw new \Exception("Token not found");
        }

        if (now()->gt($tokenRecord->expiresAt)) {
            DB::table('auth_tokens')->where('token', $refreshTokenInput)->delete();
            throw new \Exception("Token expired");
        }

        $user = User::find($tokenRecord->userId);
        if (!$user)
            throw new \Exception("User not found");

        $player = $user->player;

        $newAccessToken = $user->createToken('game-client')->plainTextToken;

        return [
            'access_token' => $newAccessToken,
            'expires_in' => 3600
        ];
    }
}