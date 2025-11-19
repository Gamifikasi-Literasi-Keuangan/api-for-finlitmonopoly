<?php

namespace App\Services;

use App\Repositories\PlayerProfileRepository;

class ProfilingService
{
    protected $profileRepo;

    public function __construct(PlayerProfileRepository $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    /**
     * Logika untuk API 6
     */
    public function getProfileDetails(string $playerId)
    {
        $profile = $this->profileRepo->findFullProfile($playerId);

        if (!$profile) {
            return null;
        }

        return $profile;
    }
}