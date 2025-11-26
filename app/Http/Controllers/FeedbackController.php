<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\FeedbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    protected $service;

    public function __construct(FeedbackService $service)
    {
        $this->service = $service;
    }

    /**
     * API 28: POST /feedback/intervention
     * Menyimpan hasil intervensi/perilaku player
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'intervention_id' => 'required|string',
            'scenario_id' => 'required|string|exists:scenarios,id',
            'player_response' => 'required|string|in:ignored,heeded,skipped'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->service->processFeedback($validator->validated());

            return response()->json([
                'ok' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Gagal menyimpan feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}