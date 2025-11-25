<?php

namespace App\Services\AI;

class CosineSimilarityService
{
    /**
     * Menghitung Cosine Similarity antara dua vektor
     */
    public function calculate(array $vectorA, array $vectorB): float
    {
        if (count($vectorA) !== count($vectorB)) {
            throw new \InvalidArgumentException('Dimensi vektor tidak sama.');
        }

        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;

        // Hitung Dot Product (A . B) dan Magnitude kuadrat (||A||^2, ||B||^2)
        foreach ($vectorA as $i => $valA) {
            $valB = $vectorB[$i];

            $dotProduct += ($valA * $valB);
            $magnitudeA += ($valA * $valA);
            $magnitudeB += ($valB * $valB);
        }

        // Akar kuadrat untuk mendapatkan Magnitude sebenarnya (||A|| dan ||B||)
        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        if ($magnitudeA * $magnitudeB == 0) {
            return 0.0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }
}