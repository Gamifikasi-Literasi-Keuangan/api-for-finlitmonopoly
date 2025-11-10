@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard & Pelaporan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- ADM-26: Dashboard Ringkasan - Metrik Utama -->
<div class="row">
    <!-- Total Pemain -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>1,245</h3>
                <p>Total Pemain</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="#" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <!-- Total Sesi -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>3,847</h3>
                <p>Total Sesi Game</p>
            </div>
            <div class="icon">
                <i class="fas fa-gamepad"></i>
            </div>
            <a href="#" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <!-- Rata-rata Skor -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>78.5</h3>
                <p>Rata-rata Skor</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <a href="#" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <!-- Kategori Populer -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>Risk Assessment</h3>
                <p>Kategori Terpopuler</p>
            </div>
            <div class="icon">
                <i class="fas fa-star"></i>
            </div>
            <a href="#" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- ADM-30: Filter Laporan -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> Filter Laporan
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="dateRange">Rentang Tanggal:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="startDate" value="{{ date('Y-m-01') }}">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text">s/d</span>
                                </div>
                                <input type="date" class="form-control" id="endDate" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="playerFilter">Filter Pemain:</label>
                            <select class="form-control" id="playerFilter">
                                <option value="">Semua Pemain</option>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="mitra">Mitra Industri</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sessionFilter">Filter Sesi:</label>
                            <select class="form-control" id="sessionFilter">
                                <option value="">Semua Sesi</option>
                                <option value="active">Sesi Aktif</option>
                                <option value="completed">Sesi Selesai</option>
                                <option value="cancelled">Sesi Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                    <i class="fas fa-search"></i> Terapkan Filter
                                </button>
                                <button type="button" class="btn btn-secondary ml-2" onclick="resetFilters()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- ADM-27: Grafik Performa - Kiri -->
    <section class="col-lg-8">
        <!-- Grafik Skor Rata-rata per Kategori -->
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i> Grafik Skor Rata-rata per Kategori
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="categoryScoreChart" style="height: 300px;"></canvas>
            </div>
        </div>
        
        <!-- Tren Permainan -->
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title">
                    <i class="fas fa-chart-line"></i> Tren Permainan Harian
                </h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm" onclick="changeTrendPeriod('7days')">7 Hari</button>
                        <button type="button" class="btn btn-info btn-sm active" onclick="changeTrendPeriod('30days')">30 Hari</button>
                        <button type="button" class="btn btn-info btn-sm" onclick="changeTrendPeriod('90days')">90 Hari</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="gameTrendChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </section>
    
    <!-- ADM-28 & ADM-29: Laporan dan Ekspor - Kanan -->
    <section class="col-lg-4">
        <!-- Laporan Hasil Permainan -->
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title">
                    <i class="fas fa-trophy"></i> Hasil Permainan Terbaru
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-warning btn-sm" onclick="refreshGameResults()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <div class="timeline">
                    <div class="time-label">
                        <span class="bg-success">{{ date('d M Y') }}</span>
                    </div>
                    
                    <div>
                        <i class="fas fa-trophy bg-warning"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ date('H:i') }}</span>
                            <h3 class="timeline-header">Sesi #3847 - Risk Assessment</h3>
                            <div class="timeline-body">
                                <strong>Pemenang:</strong> Ahmad Rizki (Score: 95)<br>
                                <strong>Pemain:</strong> 4 orang<br>
                                <strong>Durasi:</strong> 15 menit
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <i class="fas fa-gamepad bg-info"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ date('H:i', strtotime('-1 hour')) }}</span>
                            <h3 class="timeline-header">Sesi #3846 - Financial Analysis</h3>
                            <div class="timeline-body">
                                <strong>Pemenang:</strong> Sari Indah (Score: 87)<br>
                                <strong>Pemain:</strong> 6 orang<br>
                                <strong>Durasi:</strong> 18 menit
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <i class="fas fa-trophy bg-success"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ date('H:i', strtotime('-2 hours')) }}</span>
                            <h3 class="timeline-header">Sesi #3845 - Investment Strategy</h3>
                            <div class="timeline-body">
                                <strong>Pemenang:</strong> Budi Santoso (Score: 92)<br>
                                <strong>Pemain:</strong> 3 orang<br>
                                <strong>Durasi:</strong> 12 menit
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="#" class="btn btn-primary btn-sm" onclick="showAllResults()">
                    <i class="fas fa-list"></i> Lihat Semua Hasil
                </a>
            </div>
        </div>
        
        <!-- ADM-29: Ekspor Laporan -->
        <div class="card">
            <div class="card-header bg-danger">
                <h3 class="card-title">
                    <i class="fas fa-download"></i> Ekspor Laporan
                </h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Jenis Laporan:</label>
                    <select class="form-control" id="reportType">
                        <option value="game_sessions">Laporan Sesi Game</option>
                        <option value="player_performance">Performa Pemain</option>
                        <option value="category_analysis">Analisis Kategori</option>
                        <option value="score_summary">Ringkasan Skor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Format:</label>
                    <div class="btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-success active">
                            <input type="radio" name="exportFormat" value="pdf" checked> PDF
                        </label>
                        <label class="btn btn-outline-info">
                            <input type="radio" name="exportFormat" value="csv"> CSV
                        </label>
                        <label class="btn btn-outline-warning">
                            <input type="radio" name="exportFormat" value="excel"> Excel
                        </label>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-block" onclick="exportReport()">
                    <i class="fas fa-file-download"></i> Download Laporan
                </button>
            </div>
        </div>
        
        <!-- Statistik Cepat -->
        <div class="card bg-gradient-primary">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i> Statistik Hari Ini
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-center">
                        <div class="text-white">
                            <h4>42</h4>
                            <small>Sesi Aktif</small>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="text-white">
                            <h4>186</h4>
                            <small>Pemain Online</small>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6 text-center">
                        <div class="text-white">
                            <h4>82.3%</h4>
                            <small>Tingkat Completion</small>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="text-white">
                            <h4>4.7</h4>
                            <small>Rating Rata-rata</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Kategori yang Paling Sering Dipilih -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary">
                <h3 class="card-title">
                    <i class="fas fa-tags"></i> Kategori yang Paling Sering Dipilih
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <canvas id="categoryPopularityChart" style="height: 200px;"></canvas>
                    </div>
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge badge-primary">Risk Assessment</span></td>
                                    <td>1,247</td>
                                    <td>32.4%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-success">Financial Analysis</span></td>
                                    <td>986</td>
                                    <td>25.6%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-warning">Investment Strategy</span></td>
                                    <td>742</td>
                                    <td>19.3%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-info">Portfolio Management</span></td>
                                    <td>524</td>
                                    <td>13.6%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-danger">Others</span></td>
                                    <td>348</td>
                                    <td>9.1%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ADM-27: Grafik Skor Rata-rata per Kategori
    const categoryScoreCtx = document.getElementById('categoryScoreChart').getContext('2d');
    const categoryScoreChart = new Chart(categoryScoreCtx, {
        type: 'bar',
        data: {
            labels: ['Risk Assessment', 'Financial Analysis', 'Investment Strategy', 'Portfolio Management', 'Market Analysis'],
            datasets: [{
                label: 'Rata-rata Skor',
                data: [85.2, 78.9, 82.5, 76.3, 80.1],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)', 
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + '%';
                        }
                    }
                }
            }
        }
    });

    // ADM-27: Tren Permainan
    const gameTrendCtx = document.getElementById('gameTrendChart').getContext('2d');
    const gameTrendChart = new Chart(gameTrendCtx, {
        type: 'line',
        data: {
            labels: Array.from({length: 30}, (_, i) => {
                const date = new Date();
                date.setDate(date.getDate() - (29 - i));
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            }),
            datasets: [
                {
                    label: 'Sesi Game',
                    data: Array.from({length: 30}, () => Math.floor(Math.random() * 50) + 20),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4
                },
                {
                    label: 'Pemain Aktif',
                    data: Array.from({length: 30}, () => Math.floor(Math.random() * 200) + 100),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Tanggal'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Jumlah'
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            }
        }
    });

    // Kategori Popularitas Chart
    const categoryPopularityCtx = document.getElementById('categoryPopularityChart').getContext('2d');
    const categoryPopularityChart = new Chart(categoryPopularityCtx, {
        type: 'doughnut',
        data: {
            labels: ['Risk Assessment', 'Financial Analysis', 'Investment Strategy', 'Portfolio Management', 'Others'],
            datasets: [{
                data: [32.4, 25.6, 19.3, 13.6, 9.1],
                backgroundColor: [
                    '#007bff',
                    '#28a745', 
                    '#ffc107',
                    '#17a2b8',
                    '#dc3545'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
});

// ADM-30: Filter Functions
function applyFilters() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const playerFilter = document.getElementById('playerFilter').value;
    const sessionFilter = document.getElementById('sessionFilter').value;
    
    Swal.fire({
        title: 'Menerapkan Filter...',
        text: 'Sedang memfilter data laporan',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    }).then(() => {
        Swal.fire({
            title: 'Filter Diterapkan!',
            text: `Data telah difilter dari ${startDate} hingga ${endDate}`,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        // Refresh charts and data here
        refreshDashboardData();
    });
}

function resetFilters() {
    document.getElementById('startDate').value = new Date().toISOString().split('T')[0].slice(0, 8) + '01';
    document.getElementById('endDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('playerFilter').value = '';
    document.getElementById('sessionFilter').value = '';
    
    Swal.fire({
        title: 'Filter Direset',
        text: 'Semua filter telah dikembalikan ke default',
        icon: 'info',
        timer: 1000,
        showConfirmButton: false
    });
    refreshDashboardData();
}

// ADM-27: Change Trend Period
function changeTrendPeriod(period) {
    // Update active button
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Simulate data update
    Swal.fire({
        title: 'Memperbarui Grafik...',
        text: `Menampilkan data ${period === '7days' ? '7 hari' : period === '30days' ? '30 hari' : '90 hari'} terakhir`,
        icon: 'info',
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true
    });
}

// ADM-28: Game Results Functions
function refreshGameResults() {
    Swal.fire({
        title: 'Refresh Data...',
        text: 'Memuat hasil permainan terbaru',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    }).then(() => {
        location.reload();
    });
}

function showAllResults() {
    Swal.fire({
        title: 'Daftar Lengkap Hasil Permainan',
        html: `
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Sesi</th>
                            <th>Pemenang</th>
                            <th>Skor</th>
                            <th>Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>#3847</td><td>Ahmad Rizki</td><td>95</td><td>Risk Assessment</td></tr>
                        <tr><td>#3846</td><td>Sari Indah</td><td>87</td><td>Financial Analysis</td></tr>
                        <tr><td>#3845</td><td>Budi Santoso</td><td>92</td><td>Investment Strategy</td></tr>
                        <tr><td>#3844</td><td>Maya Sari</td><td>89</td><td>Portfolio Management</td></tr>
                        <tr><td>#3843</td><td>Rizky Pratama</td><td>84</td><td>Market Analysis</td></tr>
                    </tbody>
                </table>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'Tutup'
    });
}

// ADM-29: Export Functions
function exportReport() {
    const reportType = document.getElementById('reportType').value;
    const format = document.querySelector('input[name="exportFormat"]:checked').value;
    
    Swal.fire({
        title: 'Mengekspor Laporan...',
        text: `Mempersiapkan laporan ${reportType} dalam format ${format.toUpperCase()}`,
        icon: 'info',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    }).then(() => {
        // Simulate file download
        const link = document.createElement('a');
        link.href = '#';
        link.download = `laporan_${reportType}_${new Date().toISOString().split('T')[0]}.${format}`;
        
        Swal.fire({
            title: 'Berhasil!',
            text: `Laporan telah diunduh dalam format ${format.toUpperCase()}`,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    });
}

function refreshDashboardData() {
    // Simulate data refresh
    console.log('Refreshing dashboard data...');
}

// Auto-refresh data every 5 minutes
setInterval(() => {
    refreshDashboardData();
}, 300000);
</script>
@endpush

@push('styles')
<style>
.small-box {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #ddd;
    left: 31px;
    margin: 0;
    border-radius: 2px;
}

.timeline > li {
    position: relative;
    margin-right: 10px;
    margin-bottom: 15px;
}

.timeline > li:before,
.timeline > li:after {
    content: " ";
    display: table;
}

.timeline > li:after {
    clear: both;
}

.timeline > li > .timeline-item {
    background: #fff;
    border-radius: 3px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    margin-left: 60px;
    padding: 10px;
    position: relative;
}

.timeline > li > .fas,
.timeline > li > .far,
.timeline > li > .fab,
.timeline > li > .fal {
    width: 30px;
    height: 30px;
    font-size: 15px;
    line-height: 30px;
    position: absolute;
    color: #666;
    background: #d2d6de;
    border-radius: 50%;
    text-align: center;
    left: 18px;
    top: 0;
}

.timeline .time-label {
    margin-bottom: 15px;
}

.timeline .time-label > span {
    font-weight: 600;
    padding: 5px;
    display: inline-block;
    border-radius: 4px;
}

.timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding-bottom: 10px;
    font-size: 16px;
    line-height: 1.1;
}

.timeline-body {
    padding-top: 10px;
}

.time {
    color: #999;
    float: right;
    padding: 10px;
    font-size: 12px;
}

.card-header {
    position: relative;
    border-radius: 0.25rem 0.25rem 0 0;
}

.btn-group-toggle .btn {
    margin-right: 5px;
}

.badge {
    font-size: 0.75em;
}

canvas {
    max-height: 300px !important;
}
</style>
@endpush