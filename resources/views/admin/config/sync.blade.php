@extends('layouts.admin')

@section('title', 'Sinkronisasi Versi')
@section('page-title', 'Sinkronisasi Versi Game')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/config') }}">Konfigurasi Game</a></li>
    <li class="breadcrumb-item active">Sinkronisasi Versi</li>
@endsection

@section('content')
<!-- Current Version Status -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Status Versi Saat Ini
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-gamepad"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Versi Game</span>
                                <span class="info-box-number">v1.2.3</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-cogs"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Versi Config</span>
                                <span class="info-box-number">config-v2.1</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Client Aktif</span>
                                <span class="info-box-number">24</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sync Terakhir</span>
                                <span class="info-box-number">{{ date('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sync Control Panel -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-sync"></i> Panel Sinkronisasi
                </h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Pilih Target Sinkronisasi:</label>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="syncAll" checked onchange="toggleSyncOptions()">
                        <label class="custom-control-label" for="syncAll">Sinkronisasi Semua Client</label>
                    </div>
                    <div class="mt-2" id="specificOptions" style="display: none;">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="syncConfig">
                            <label class="custom-control-label" for="syncConfig">Konfigurasi Game</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="syncAssets">
                            <label class="custom-control-label" for="syncAssets">Asset Game</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="syncRules">
                            <label class="custom-control-label" for="syncRules">Aturan Permainan</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="syncQuestions">
                            <label class="custom-control-label" for="syncQuestions">Database Soal</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="syncMode">Mode Sinkronisasi:</label>
                    <select class="form-control" id="syncMode" name="syncMode">
                        <option value="immediate">Immediate - Langsung sync semua client</option>
                        <option value="graceful">Graceful - Tunggu client selesai bermain</option>
                        <option value="scheduled">Scheduled - Jadwalkan untuk nanti</option>
                    </select>
                </div>
                
                <div class="form-group" id="scheduledTime" style="display: none;">
                    <label for="syncTime">Waktu Sinkronisasi:</label>
                    <input type="datetime-local" class="form-control" id="syncTime" name="syncTime">
                </div>
                
                <div class="mt-4">
                    <button type="button" class="btn btn-success btn-lg" onclick="startSync()">
                        <i class="fas fa-sync"></i> Mulai Sinkronisasi
                    </button>
                    <button type="button" class="btn btn-warning" onclick="testConnection()">
                        <i class="fas fa-plug"></i> Test Koneksi
                    </button>
                    <button type="button" class="btn btn-info" onclick="viewSyncHistory()">
                        <i class="fas fa-history"></i> Riwayat Sync
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sync Progress -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i> Progress Sinkronisasi
                </h3>
            </div>
            <div class="card-body">
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%" id="syncProgressBar">
                        <span id="syncProgressText">100%</span>
                    </div>
                </div>
                
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Client Tersync</span>
                        <span class="info-box-number" id="syncedClients">24/24</span>
                    </div>
                </div>
                
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Client Gagal</span>
                        <span class="info-box-number" id="failedClients">0/24</span>
                    </div>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">Status: <span id="syncStatus" class="badge badge-success">Selesai</span></small>
                    <br>
                    <small class="text-muted">Estimasi: <span id="syncEta">-</span></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Client Status Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Status Client Terdaftar
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-primary" onclick="refreshClientList()">
                        <i class="fas fa-refresh"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="clientTable">
                        <thead>
                            <tr>
                                <th>ID Client</th>
                                <th>IP Address</th>
                                <th>Versi Game</th>
                                <th>Versi Config</th>
                                <th>Status Koneksi</th>
                                <th>Terakhir Sync</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>CLIENT-001</td>
                                <td>192.168.1.10</td>
                                <td><span class="badge badge-success">v1.2.3</span></td>
                                <td><span class="badge badge-success">config-v2.1</span></td>
                                <td><span class="badge badge-success"><i class="fas fa-circle"></i> Online</span></td>
                                <td>{{ date('H:i:s') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="syncClient('CLIENT-001')">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>CLIENT-002</td>
                                <td>192.168.1.11</td>
                                <td><span class="badge badge-success">v1.2.3</span></td>
                                <td><span class="badge badge-success">config-v2.1</span></td>
                                <td><span class="badge badge-success"><i class="fas fa-circle"></i> Online</span></td>
                                <td>{{ date('H:i:s', strtotime('-2 minutes')) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="syncClient('CLIENT-002')">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>CLIENT-003</td>
                                <td>192.168.1.12</td>
                                <td><span class="badge badge-warning">v1.2.2</span></td>
                                <td><span class="badge badge-warning">config-v2.0</span></td>
                                <td><span class="badge badge-success"><i class="fas fa-circle"></i> Online</span></td>
                                <td>{{ date('H:i:s', strtotime('-10 minutes')) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="syncClient('CLIENT-003')">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>CLIENT-004</td>
                                <td>192.168.1.13</td>
                                <td><span class="badge badge-danger">v1.2.1</span></td>
                                <td><span class="badge badge-danger">config-v1.9</span></td>
                                <td><span class="badge badge-danger"><i class="fas fa-circle"></i> Offline</span></td>
                                <td>{{ date('H:i:s', strtotime('-30 minutes')) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" disabled>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sync Log -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">
                    <i class="fas fa-terminal"></i> Log Sinkronisasi Real-time
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="clearLog()">
                        <i class="fas fa-trash"></i> Clear Log
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="syncLog" style="height: 300px; overflow-y: auto; background: #2c3e50; color: #ecf0f1; padding: 15px; font-family: monospace; font-size: 13px;">
                    <div class="log-entry">[{{ date('H:i:s') }}] INFO: Sistem sinkronisasi siap</div>
                    <div class="log-entry">[{{ date('H:i:s') }}] INFO: 24 client terdeteksi online</div>
                    <div class="log-entry">[{{ date('H:i:s') }}] INFO: Versi server: v1.2.3 (config-v2.1)</div>
                    <div class="log-entry">[{{ date('H:i:s') }}] WARNING: CLIENT-003 menggunakan versi lama</div>
                    <div class="log-entry">[{{ date('H:i:s') }}] ERROR: CLIENT-004 tidak dapat dijangkau</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSyncOptions() {
    const syncAll = document.getElementById('syncAll');
    const specificOptions = document.getElementById('specificOptions');
    
    if (syncAll.checked) {
        specificOptions.style.display = 'none';
    } else {
        specificOptions.style.display = 'block';
    }
}

document.getElementById('syncMode').addEventListener('change', function() {
    const scheduledTime = document.getElementById('scheduledTime');
    if (this.value === 'scheduled') {
        scheduledTime.style.display = 'block';
    } else {
        scheduledTime.style.display = 'none';
    }
});

function addLogEntry(message, type = 'INFO') {
    const log = document.getElementById('syncLog');
    const timestamp = new Date().toLocaleTimeString();
    const colorClass = type === 'ERROR' ? 'text-danger' : type === 'WARNING' ? 'text-warning' : type === 'SUCCESS' ? 'text-success' : '';
    
    const entry = document.createElement('div');
    entry.className = 'log-entry ' + colorClass;
    entry.innerHTML = `[${timestamp}] ${type}: ${message}`;
    
    log.appendChild(entry);
    log.scrollTop = log.scrollHeight;
}

function startSync() {
    const mode = document.getElementById('syncMode').value;
    
    if (mode === 'scheduled') {
        const syncTime = document.getElementById('syncTime').value;
        if (!syncTime) {
            Swal.fire('Error', 'Pilih waktu untuk sinkronisasi terjadwal', 'error');
            return;
        }
    }
    
    Swal.fire({
        title: 'Konfirmasi Sinkronisasi',
        text: `Mulai sinkronisasi dengan mode: ${mode}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Mulai!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            performSync();
        }
    });
}

function performSync() {
    // Reset progress
    document.getElementById('syncProgressBar').style.width = '0%';
    document.getElementById('syncProgressText').textContent = '0%';
    document.getElementById('syncStatus').textContent = 'Berjalan';
    document.getElementById('syncStatus').className = 'badge badge-warning';
    
    addLogEntry('Memulai proses sinkronisasi...', 'INFO');
    addLogEntry('Memeriksa koneksi ke semua client...', 'INFO');
    
    // Simulate sync progress
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress > 100) progress = 100;
        
        document.getElementById('syncProgressBar').style.width = progress + '%';
        document.getElementById('syncProgressText').textContent = Math.round(progress) + '%';
        document.getElementById('syncedClients').textContent = Math.round(progress * 24 / 100) + '/24';
        
        if (progress < 30) {
            addLogEntry(`Mengirim konfigurasi ke CLIENT-00${Math.floor(Math.random() * 9) + 1}...`, 'INFO');
        } else if (progress < 60) {
            addLogEntry(`CLIENT-00${Math.floor(Math.random() * 9) + 1} berhasil disinkronisasi`, 'SUCCESS');
        } else if (progress < 90) {
            addLogEntry(`Memverifikasi integritas data...`, 'INFO');
        }
        
        if (progress >= 100) {
            clearInterval(interval);
            document.getElementById('syncStatus').textContent = 'Selesai';
            document.getElementById('syncStatus').className = 'badge badge-success';
            document.getElementById('syncEta').textContent = '-';
            addLogEntry('Sinkronisasi berhasil diselesaikan!', 'SUCCESS');
            addLogEntry('Semua client telah tersinkronisasi dengan versi terbaru', 'SUCCESS');
            
            Swal.fire({
                title: 'Berhasil!',
                text: 'Sinkronisasi telah selesai',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            const eta = Math.round((100 - progress) / 20 * 5);
            document.getElementById('syncEta').textContent = eta + ' detik';
        }
    }, 1000);
}

function testConnection() {
    addLogEntry('Testing koneksi ke semua client...', 'INFO');
    
    setTimeout(() => {
        addLogEntry('CLIENT-001: OK (latency: 15ms)', 'SUCCESS');
        addLogEntry('CLIENT-002: OK (latency: 23ms)', 'SUCCESS');
        addLogEntry('CLIENT-003: OK (latency: 45ms)', 'WARNING');
        addLogEntry('CLIENT-004: FAILED (timeout)', 'ERROR');
        
        Swal.fire({
            title: 'Test Koneksi Selesai',
            html: 'Hasil:<br>✅ 3 client online<br>❌ 1 client offline',
            icon: 'info'
        });
    }, 2000);
}

function syncClient(clientId) {
    addLogEntry(`Melakukan sync individual untuk ${clientId}...`, 'INFO');
    
    setTimeout(() => {
        addLogEntry(`${clientId} berhasil disinkronisasi`, 'SUCCESS');
        Swal.fire({
            title: 'Berhasil',
            text: `${clientId} telah disinkronisasi`,
            icon: 'success',
            timer: 1000,
            showConfirmButton: false
        });
    }, 1500);
}

function refreshClientList() {
    addLogEntry('Memperbarui daftar client...', 'INFO');
    
    setTimeout(() => {
        addLogEntry('Daftar client telah diperbarui', 'SUCCESS');
        location.reload();
    }, 1000);
}

function viewSyncHistory() {
    Swal.fire({
        title: 'Riwayat Sinkronisasi',
        html: `
            <div class="text-left">
                <p><strong>{{ date('d/m/Y H:i') }}</strong> - Sync semua client (24/24 berhasil)</p>
                <p><strong>{{ date('d/m/Y H:i', strtotime('-2 hours')) }}</strong> - Sync konfigurasi (23/24 berhasil)</p>
                <p><strong>{{ date('d/m/Y H:i', strtotime('-6 hours')) }}</strong> - Sync database soal (24/24 berhasil)</p>
                <p><strong>{{ date('d/m/Y H:i', strtotime('-1 day')) }}</strong> - Update versi game (22/24 berhasil)</p>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'Tutup'
    });
}

function clearLog() {
    document.getElementById('syncLog').innerHTML = '';
    addLogEntry('Log telah dibersihkan', 'INFO');
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    setInterval(() => {
        if (Math.random() > 0.8) {
            const events = [
                'Client baru terhubung',
                'Heartbeat check completed',
                'Memory usage: normal',
                'Network latency: optimal'
            ];
            const event = events[Math.floor(Math.random() * events.length)];
            addLogEntry(event, 'INFO');
        }
    }, 10000);
});
</script>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('styles')
<style>
.log-entry {
    margin-bottom: 2px;
    line-height: 1.4;
}
.progress {
    height: 25px;
}
.progress-bar {
    font-weight: bold;
}
.table td {
    vertical-align: middle;
}
.badge {
    font-size: 0.85em;
}
#syncLog {
    border-radius: 5px;
    border: 1px solid #34495e;
}
.card-header.bg-dark {
    color: white;
}
</style>
@endpush