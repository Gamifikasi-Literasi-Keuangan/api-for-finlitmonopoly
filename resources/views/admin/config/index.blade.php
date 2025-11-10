@extends('layouts.admin')

@section('title', 'Konfigurasi Game')
@section('page-title', 'Lihat Konfigurasi Game')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/config') }}">Konfigurasi Game</a></li>
    <li class="breadcrumb-item active">Lihat Konfigurasi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Main Config Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs mr-2"></i>
                    Data Konfigurasi Game Saat Ini
                </h3>
                <div class="card-tools">
                    <a href="{{ url('/admin/config/edit') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit Konfigurasi
                    </a>
                    <button type="button" class="btn btn-success btn-sm" onclick="syncConfig()">
                        <i class="fas fa-sync"></i> Sinkronisasi
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Basic Settings -->
                    <div class="col-md-6">
                        <h5><i class="fas fa-users text-primary"></i> Pengaturan Pemain</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Jumlah Pemain Minimum:</strong></td>
                                <td><span class="badge badge-info">2 pemain</span></td>
                            </tr>
                            <tr>
                                <td><strong>Jumlah Pemain Maksimum:</strong></td>
                                <td><span class="badge badge-warning">6 pemain</span></td>
                            </tr>
                            <tr>
                                <td><strong>Pemain per Room:</strong></td>
                                <td><span class="badge badge-secondary">4 pemain</span></td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Version Info -->
                    <div class="col-md-6">
                        <h5><i class="fas fa-code-branch text-success"></i> Informasi Versi</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Versi Game:</strong></td>
                                <td><span class="badge badge-success">v1.2.3</span></td>
                            </tr>
                            <tr>
                                <td><strong>Versi Konfigurasi:</strong></td>
                                <td><span class="badge badge-primary">config-v2.1</span></td>
                            </tr>
                            <tr>
                                <td><strong>Terakhir Diperbarui:</strong></td>
                                <td>{{ date('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <!-- Game Rules Settings -->
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-gamepad text-danger"></i> Aturan Permainan</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Waktu per Turn:</strong></td>
                                <td>30 detik</td>
                            </tr>
                            <tr>
                                <td><strong>Total Rounds:</strong></td>
                                <td>5 rounds</td>
                            </tr>
                            <tr>
                                <td><strong>Mode Permainan:</strong></td>
                                <td><span class="badge badge-info">Risk Assessment</span></td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Server Settings -->
                    <div class="col-md-6">
                        <h5><i class="fas fa-server text-info"></i> Pengaturan Server</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Server Status:</strong></td>
                                <td><span class="badge badge-success">Online</span></td>
                            </tr>
                            <tr>
                                <td><strong>Maintenance Mode:</strong></td>
                                <td><span class="badge badge-secondary">Disabled</span></td>
                            </tr>
                            <tr>
                                <td><strong>Max Concurrent Games:</strong></td>
                                <td>50 games</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Configuration Details Cards -->
<div class="row">
    <!-- Scoring System -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">
                    <i class="fas fa-chart-line"></i> Sistem Penilaian
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Jawaban Benar: +10 poin</li>
                    <li><i class="fas fa-times text-danger"></i> Jawaban Salah: -5 poin</li>
                    <li><i class="fas fa-clock text-warning"></i> Bonus Waktu: +2 poin</li>
                    <li><i class="fas fa-fire text-info"></i> Streak Bonus: +5 poin</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Difficulty Settings -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title">
                    <i class="fas fa-sliders-h"></i> Tingkat Kesulitan
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><span class="badge badge-success">Easy</span> 40% soal</li>
                    <li><span class="badge badge-warning">Medium</span> 40% soal</li>
                    <li><span class="badge badge-danger">Hard</span> 20% soal</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Sync Status -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-sync"></i> Status Sinkronisasi
                </h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Terakhir Sync</span>
                        <span class="info-box-number">{{ date('H:i') }}</span>
                    </div>
                </div>
                <small class="text-muted">Semua client telah tersinkronisasi</small>
            </div>
        </div>
    </div>
</div>

<!-- JSON Config Display -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-code"></i> Raw Configuration Data (JSON)
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3"><code>{
  "minPlayers": 2,
  "maxPlayers": 6,
  "defaultPlayersPerRoom": 4,
  "gameVersion": "1.2.3",
  "configVersion": "config-v2.1",
  "turnTimeLimit": 30,
  "totalRounds": 5,
  "gameMode": "risk_assessment",
  "scoring": {
    "correctAnswer": 10,
    "wrongAnswer": -5,
    "timeBonus": 2,
    "streakBonus": 5
  },
  "difficulty": {
    "easy": 40,
    "medium": 40,
    "hard": 20
  },
  "server": {
    "maxConcurrentGames": 50,
    "maintenanceMode": false,
    "status": "online"
  },
  "lastUpdated": "{{ date('Y-m-d H:i:s') }}"
}</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function syncConfig() {
    // Simulate sync process
    Swal.fire({
        title: 'Sinkronisasi Konfigurasi',
        text: 'Melakukan sinkronisasi dengan semua client...',
        icon: 'info',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    }).then(() => {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Konfigurasi telah disinkronisasi dengan semua client',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    });
}
</script>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('styles')
<style>
.table td {
    vertical-align: middle;
}
.badge {
    font-size: 0.9em;
}
pre code {
    font-size: 0.85em;
    line-height: 1.4;
}
</style>
@endpush