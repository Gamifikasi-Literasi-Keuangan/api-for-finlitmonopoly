@extends('layouts.admin')

@section('title', 'Daftar Player')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4>Daftar Player</h4>
            <button class="btn btn-sm btn-primary" onclick="loadPlayers()">Refresh</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="playersTable">
                <thead>
                    <tr>
                        <th>Player ID</th>
                        <th>Username</th>
                        <th>Locale</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="playersTbody">
                    <tr><td colspan="6" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function loadPlayers() {
    const tbody = document.getElementById('playersTbody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';
    try {
        const res = await fetch('{{ url('/api/players') }}');
        const json = await res.json();
        const players = json.data || [];
        if (!players.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No players</td></tr>';
            return;
        }

        // Build rows: main row + hidden details row (profiling sublist)
        tbody.innerHTML = players.map(p => {
            return `
            <tr id="player-row-${p.id}">
                <td>${p.id}</td>
                <td>${p.username}</td>
                <td>${p.locale}</td>
                <td>${p.created_at}</td>
                <td><span class="badge bg-${p.status === 'connected' ? 'success' : 'secondary'}">${p.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleProfiling(${p.id}, this)">Profiling</button>
                </td>
            </tr>
            <tr id="player-details-${p.id}" class="d-none">
                <td colspan="6">
                    <div id="player-details-content-${p.id}">
                        <!-- profiling content will be loaded here -->
                    </div>
                </td>
            </tr>
            `;
        }).join('');
    } catch (err) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-danger">Error loading players</td></tr>';
        console.error(err);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadPlayers();
});

async function toggleProfiling(id, btn) {
    const detailsRow = document.getElementById('player-details-' + id);
    const contentDiv = document.getElementById('player-details-content-' + id);
    if (!detailsRow || !contentDiv) return;

    if (detailsRow.classList.contains('d-none')) {
        // show
        if (contentDiv.dataset.loaded !== '1') {
            contentDiv.innerHTML = '<div class="text-center py-2">Loading profiling...</div>';
            try {
                const url = '{{ url('/profiling/details') }}?player_id=' + encodeURIComponent(id);
                const res = await fetch(url);
                const json = await res.json();
                // Build a compact profiling summary
                const prof = json || {};
                let html = '<div class="p-2">';
                if (prof.current_scores) {
                    html += '<h6>Current Scores</h6><div class="d-flex flex-wrap gap-2 mb-2">';
                    for (const [k,v] of Object.entries(prof.current_scores)) {
                        html += `<span class="badge bg-info text-dark me-1">${k}: ${v}</span>`;
                    }
                    html += '</div>';
                }
                if (prof.weak_areas && prof.weak_areas.length) {
                    html += '<h6>Weak Areas</h6><ul>' + prof.weak_areas.map(a => `<li>${a}</li>`).join('') + '</ul>';
                }
                if (prof.recommended_focus && prof.recommended_focus.length) {
                    html += '<h6>Recommended Focus</h6><ul>' + prof.recommended_focus.map(a => `<li>${a}</li>`).join('') + '</ul>';
                }
                if (prof.behavioral_patterns && prof.behavioral_patterns.length) {
                    html += '<h6>Behavioral Patterns</h6><ul>' + prof.behavioral_patterns.map(a => `<li>${a}</li>`).join('') + '</ul>';
                }
                html += '</div>';
                contentDiv.innerHTML = html;
                contentDiv.dataset.loaded = '1';
            } catch (e) {
                contentDiv.innerHTML = '<div class="text-danger">Error loading profiling</div>';
                console.error(e);
            }
        }
        detailsRow.classList.remove('d-none');
        btn.textContent = 'Hide Profiling';
    } else {
        // hide
        detailsRow.classList.add('d-none');
        btn.textContent = 'Profiling';
    }
}
</script>

@endsection
