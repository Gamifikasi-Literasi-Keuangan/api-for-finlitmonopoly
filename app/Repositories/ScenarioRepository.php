<?php

namespace App\Repositories;

use App\Models\Scenario;

class ScenarioRepository
{

    public function getAllForAnalysis()
    {
        return Scenario::select('id', 'title', 'category', 'weak_area_relevance', 'cluster_relevance', 'difficulty')
            ->get();
    }

    public function findById($id)
    {
        return Scenario::with('options')->find($id);
    }
}