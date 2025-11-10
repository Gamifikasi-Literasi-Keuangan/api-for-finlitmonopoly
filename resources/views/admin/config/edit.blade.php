@extends('layouts.admin')

@section('title', 'Edit Konfigurasi')
@section('page-title', 'Ubah Konfigurasi Game')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/config') }}">Konfigurasi Game</a></li>
    <li class="breadcrumb-item active">Edit Konfigurasi</li>
@endsection

@section('content')
<form id="configForm" onsubmit="submitConfig(event)">
    <div class="row">
        <!-- Player Settings -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Pengaturan Pemain
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="minPlayers">Jumlah Pemain Minimum <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="minPlayers" name="minPlayers" value="2" min="1" max="10" required>
                        <small class="form-text text-muted">Minimal 1 pemain diperlukan untuk memulai game</small>
                    </div>
                    <div class="form-group">
                        <label for="maxPlayers">Jumlah Pemain Maksimum <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="maxPlayers" name="maxPlayers" value="6" min="2" max="20" required>
                        <small class="form-text text-muted">Maksimal pemain yang dapat bergabung dalam satu game</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultPlayersPerRoom">Pemain per Room Default</label>
                        <input type="number" class="form-control" id="defaultPlayersPerRoom" name="defaultPlayersPerRoom" value="4" min="2" max="10">
                        <small class="form-text text-muted">Jumlah pemain default untuk setiap room</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Version Settings -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-code-branch"></i> Informasi Versi
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="gameVersion">Versi Game <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="gameVersion" name="gameVersion" value="1.2.3" pattern="[0-9]+\.[0-9]+\.[0-9]+" required>
                        <small class="form-text text-muted">Format: x.y.z (contoh: 1.2.3)</small>
                    </div>
                    <div class="form-group">
                        <label for="configVersion">Versi Konfigurasi</label>
                        <input type="text" class="form-control" id="configVersion" name="configVersion" value="config-v2.1" readonly>
                        <small class="form-text text-muted">Versi akan diupdate otomatis saat menyimpan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Game Rules -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">
                        <i class="fas fa-gamepad"></i> Aturan Permainan
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="turnTimeLimit">Waktu per Turn (detik) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="turnTimeLimit" name="turnTimeLimit" value="30" min="10" max="300" required>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="turnTimeLimitPreset" value="15" onchange="setTimeLimit(15)">
                                <label class="form-check-label">15s</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="turnTimeLimitPreset" value="30" checked onchange="setTimeLimit(30)">
                                <label class="form-check-label">30s</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="turnTimeLimitPreset" value="60" onchange="setTimeLimit(60)">
                                <label class="form-check-label">60s</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="totalRounds">Total Rounds <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="totalRounds" name="totalRounds" value="5" min="1" max="20" required>
                        <small class="form-text text-muted">Jumlah putaran dalam satu permainan</small>
                    </div>
                    <div class="form-group">
                        <label for="gameMode">Mode Permainan</label>
                        <select class="form-control" id="gameMode" name="gameMode">
                            <option value="risk_assessment" selected>Risk Assessment</option>
                            <option value="time_attack">Time Attack</option>
                            <option value="survival">Survival</option>
                            <option value="tournament">Tournament</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Server Settings -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-server"></i> Pengaturan Server
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="maxConcurrentGames">Max Concurrent Games</label>
                        <input type="number" class="form-control" id="maxConcurrentGames" name="maxConcurrentGames" value="50" min="1" max="1000">
                        <small class="form-text text-muted">Maksimal game yang dapat berjalan bersamaan</small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="maintenanceMode" name="maintenanceMode">
                            <label class="custom-control-label" for="maintenanceMode">Mode Maintenance</label>
                        </div>
                        <small class="form-text text-muted">Aktifkan untuk mencegah user baru bergabung</small>
                    </div>
                    <div class="form-group">
                        <label>Status Server</label>
                        <div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="statusOnline" name="serverStatus" class="custom-control-input" value="online" checked>
                                <label class="custom-control-label" for="statusOnline">Online</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="statusOffline" name="serverStatus" class="custom-control-input" value="offline">
                                <label class="custom-control-label" for="statusOffline">Offline</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scoring System -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-danger">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Sistem Penilaian
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="correctAnswer">Jawaban Benar</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+</span>
                                    </div>
                                    <input type="number" class="form-control" id="correctAnswer" name="correctAnswer" value="10" min="1" max="100">
                                    <div class="input-group-append">
                                        <span class="input-group-text">poin</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="wrongAnswer">Jawaban Salah</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">-</span>
                                    </div>
                                    <input type="number" class="form-control" id="wrongAnswer" name="wrongAnswer" value="5" min="0" max="50">
                                    <div class="input-group-append">
                                        <span class="input-group-text">poin</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="timeBonus">Bonus Waktu</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+</span>
                                    </div>
                                    <input type="number" class="form-control" id="timeBonus" name="timeBonus" value="2" min="0" max="20">
                                    <div class="input-group-append">
                                        <span class="input-group-text">poin</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="streakBonus">Streak Bonus</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+</span>
                                    </div>
                                    <input type="number" class="form-control" id="streakBonus" name="streakBonus" value="5" min="0" max="50">
                                    <div class="input-group-append">
                                        <span class="input-group-text">poin</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Difficulty Settings -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title">
                        <i class="fas fa-sliders-h"></i> Tingkat Kesulitan (%)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="difficultyEasy">Easy</label>
                                <div class="input-group">
                                    <input type="range" class="form-control-range" id="difficultyEasy" name="difficultyEasy" min="0" max="100" value="40" oninput="updateDifficultyDisplay()">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="easyPercent">40%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="difficultyMedium">Medium</label>
                                <div class="input-group">
                                    <input type="range" class="form-control-range" id="difficultyMedium" name="difficultyMedium" min="0" max="100" value="40" oninput="updateDifficultyDisplay()">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="mediumPercent">40%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="difficultyHard">Hard</label>
                                <div class="input-group">
                                    <input type="range" class="form-control-range" id="difficultyHard" name="difficultyHard" min="0" max="100" value="20" oninput="updateDifficultyDisplay()">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="hardPercent">20%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Total:</strong> <span id="totalPercent">100%</span> 
                        <span id="percentageWarning" class="text-danger" style="display: none;">- Persentase harus berjumlah 100%!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="float-left">
                        <a href="{{ url('/admin/config') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="button" class="btn btn-warning" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                    <div class="float-right">
                        <button type="button" class="btn btn-info" onclick="previewConfig()">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Konfigurasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function setTimeLimit(seconds) {
    document.getElementById('turnTimeLimit').value = seconds;
}

function updateDifficultyDisplay() {
    const easy = parseInt(document.getElementById('difficultyEasy').value);
    const medium = parseInt(document.getElementById('difficultyMedium').value);
    const hard = parseInt(document.getElementById('difficultyHard').value);
    
    document.getElementById('easyPercent').textContent = easy + '%';
    document.getElementById('mediumPercent').textContent = medium + '%';
    document.getElementById('hardPercent').textContent = hard + '%';
    
    const total = easy + medium + hard;
    document.getElementById('totalPercent').textContent = total + '%';
    
    const warning = document.getElementById('percentageWarning');
    if (total !== 100) {
        warning.style.display = 'inline';
    } else {
        warning.style.display = 'none';
    }
}

function resetForm() {
    Swal.fire({
        title: 'Reset Form?',
        text: 'Semua perubahan akan dikembalikan ke nilai awal',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('configForm').reset();
            updateDifficultyDisplay();
            Swal.fire('Reset!', 'Form telah dikembalikan ke nilai awal', 'success');
        }
    });
}

function previewConfig() {
    const formData = new FormData(document.getElementById('configForm'));
    const config = {};
    
    for (let [key, value] of formData.entries()) {
        config[key] = value;
    }
    
    Swal.fire({
        title: 'Preview Konfigurasi',
        html: '<pre class="text-left">' + JSON.stringify(config, null, 2) + '</pre>',
        width: '800px',
        showConfirmButton: true,
        confirmButtonText: 'Tutup'
    });
}

function submitConfig(event) {
    event.preventDefault();
    
    // Validate difficulty percentages
    const easy = parseInt(document.getElementById('difficultyEasy').value);
    const medium = parseInt(document.getElementById('difficultyMedium').value);
    const hard = parseInt(document.getElementById('difficultyHard').value);
    
    if (easy + medium + hard !== 100) {
        Swal.fire({
            title: 'Error!',
            text: 'Total persentase kesulitan harus 100%',
            icon: 'error'
        });
        return;
    }
    
    // Simulate saving
    Swal.fire({
        title: 'Menyimpan Konfigurasi...',
        text: 'Mohon tunggu, sedang memproses perubahan',
        icon: 'info',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    }).then(() => {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Konfigurasi game telah disimpan',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = '{{ url("/admin/config") }}';
        });
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateDifficultyDisplay();
});
</script>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('styles')
<style>
.form-control-range {
    width: 100%;
}
.input-group .form-control-range {
    border: none;
    margin-right: 10px;
}
.card-header {
    color: white;
}
.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
}
pre {
    text-align: left;
    max-height: 400px;
    overflow-y: auto;
}
</style>
@endpush