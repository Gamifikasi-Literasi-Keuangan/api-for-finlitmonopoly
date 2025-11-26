<?php

namespace App\Services;

use App\Repositories\FeedbackRepository;
use App\Services\ThresholdService;

class FeedbackService
{
    protected $feedbackRepo;
    protected $thresholdService;

    // Inject ThresholdService agar kita bisa update threshold dari sini
    public function __construct(FeedbackRepository $feedbackRepo, ThresholdService $thresholdService)
    {
        $this->feedbackRepo = $feedbackRepo;
        $this->thresholdService = $thresholdService;
    }

    public function processFeedback(array $data)
    {
        // 1. Selalu Catat Log
        $this->feedbackRepo->logIntervention($data);

        // 2. Jika butuh update threshold, bisa ditambahkan logic di sini
        // Contoh: jika player_response = 'ignored', tingkatkan sensitivitas
        // (Opsional, sesuai kebutuhan business logic)

        return [
            'logged' => true,
            'effectiveness_updated' => false
        ];
    }
}