@extends('layouts.admin')

@section('title', 'Profiling Player')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4>Profiling Player: {{ $playerId }}</h4>
            <p class="text-muted">Data diambil dari endpoint <code>/profiling/details</code> dan <code>/profiling/cluster</code></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Current Scores</div>
                <div class="card-body" id="scoresCard">Memuat...</div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Weak Areas</div>
                <div class="card-body" id="weakAreas">Memuat...</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Recommended Focus</div>
                <div class="card-body" id="recommendedFocus">Memuat...</div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Behavioral Patterns</div>
                <div class="card-body" id="behavioralPatterns">Memuat...</div>
            </div>
        </div>
    </div>
</div>

<script>
async function loadProfiling() {
    const playerId = '{{ $playerId }}';
    const scoresEl = document.getElementById('scoresCard');
    const weakEl = document.getElementById('weakAreas');
    const recEl = document.getElementById('recommendedFocus');
    const behEl = document.getElementById('behavioralPatterns');

    try {
        const [detRes, cluRes] = await Promise.all([
            fetch(`{{ url('/profiling/details') }}?player_id=${playerId}`),
            fetch(`{{ url('/profiling/cluster') }}?player_id=${playerId}`),
        ]);

        const details = await detRes.json();
        const cluster = await cluRes.json();

        scoresEl.innerHTML = Object.entries(details.current_scores).map(([k,v]) => `<div><strong>${k}</strong>: ${v}</div>`).join('');

        weakEl.innerHTML = details.weak_areas.map(w => `<div>${w.area} — ${w.score}</div>`).join('') || '<div>-</div>';

        recEl.innerHTML = `<div>Practice: ${details.recommended_focus.practice_scenarios.title || '-'}</div>` +
            `<div>Micro quizzes: ${details.recommended_focus.micro_quizzes ? 'Yes' : 'No'}</div>`;

        behEl.innerHTML = Object.entries(details.behavioral_patterns).map(([k,v]) => `<div><strong>${k}</strong>: ${v}</div>`).join('') +
            `<hr/><div><strong>Cluster</strong>: ${cluster.cluster.label} (id ${cluster.cluster.id})</div>`;

    } catch (err) {
        scoresEl.innerHTML = '<div class="text-danger">Error loading profiling</div>';
        console.error(err);
    }
}

document.addEventListener('DOMContentLoaded', loadProfiling);
</script>

@endsection
