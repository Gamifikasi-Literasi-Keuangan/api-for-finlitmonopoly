@extends('layouts.admin')

@section('title', 'Skenario Pertanyaan')
@section('page-title', 'Manajemen Skenario Pertanyaan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/content/scenarios') }}">Manajemen Konten</a></li>
    <li class="breadcrumb-item active">Skenario Pertanyaan</li>
@endsection

@section('content')
<!-- ADM-21: Lihat Skenario Pertanyaan -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">
                    <i class="fas fa-list-alt"></i> Daftar Skenario Pertanyaan
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success" onclick="addScenario()">
                        <i class="fas fa-plus"></i> Tambah Skenario
                    </button>
                    <button type="button" class="btn btn-info" onclick="refreshScenarios()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter dan Search -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="categoryFilter">Filter Kategori:</label>
                            <select class="form-control" id="categoryFilter" onchange="filterScenarios()">
                                <option value="">Semua Kategori</option>
                                <option value="risk_assessment">Risk Assessment</option>
                                <option value="financial_analysis">Financial Analysis</option>
                                <option value="investment_strategy">Investment Strategy</option>
                                <option value="portfolio_management">Portfolio Management</option>
                                <option value="market_analysis">Market Analysis</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="difficultyFilter">Filter Tingkat Kesulitan:</label>
                            <select class="form-control" id="difficultyFilter" onchange="filterScenarios()">
                                <option value="">Semua Tingkat</option>
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="searchBox">Pencarian:</label>
                            <input type="text" class="form-control" id="searchBox" placeholder="Cari skenario..." onkeyup="searchScenarios()">
                        </div>
                    </div>
                </div>
                
                <!-- Tabel Skenario -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="scenariosTable">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Kategori</th>
                                <th width="40%">Pertanyaan</th>
                                <th width="10%">Tingkat</th>
                                <th width="10%">Status</th>
                                <th width="10%">Dibuat</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Mock Data Scenarios -->
                            <tr data-category="risk_assessment" data-difficulty="medium">
                                <td>1</td>
                                <td><span class="badge badge-primary">Risk Assessment</span></td>
                                <td>
                                    <strong>Analisis Risiko Investasi Saham</strong><br>
                                    <small class="text-muted">Anda diminta untuk menganalisis risiko investasi pada saham teknologi yang volatil...</small>
                                </td>
                                <td><span class="badge badge-warning">Medium</span></td>
                                <td><span class="badge badge-success">Aktif</span></td>
                                <td>{{ date('d/m/Y', strtotime('-2 days')) }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-info" onclick="viewScenario(1)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editScenario(1)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteScenario(1)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr data-category="financial_analysis" data-difficulty="hard">
                                <td>2</td>
                                <td><span class="badge badge-success">Financial Analysis</span></td>
                                <td>
                                    <strong>Evaluasi Kinerja Keuangan Perusahaan</strong><br>
                                    <small class="text-muted">Berdasarkan laporan keuangan PT. ABC, analisis rasio keuangan dan berikan rekomendasi...</small>
                                </td>
                                <td><span class="badge badge-danger">Hard</span></td>
                                <td><span class="badge badge-success">Aktif</span></td>
                                <td>{{ date('d/m/Y', strtotime('-1 day')) }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-info" onclick="viewScenario(2)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editScenario(2)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteScenario(2)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr data-category="investment_strategy" data-difficulty="easy">
                                <td>3</td>
                                <td><span class="badge badge-warning">Investment Strategy</span></td>
                                <td>
                                    <strong>Strategi Investasi untuk Pemula</strong><br>
                                    <small class="text-muted">Seorang fresh graduate ingin memulai investasi dengan modal terbatas. Apa strategi terbaik?</small>
                                </td>
                                <td><span class="badge badge-success">Easy</span></td>
                                <td><span class="badge badge-secondary">Draft</span></td>
                                <td>{{ date('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-info" onclick="viewScenario(3)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editScenario(3)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteScenario(3)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr data-category="portfolio_management" data-difficulty="medium">
                                <td>4</td>
                                <td><span class="badge badge-info">Portfolio Management</span></td>
                                <td>
                                    <strong>Diversifikasi Portfolio Optimal</strong><br>
                                    <small class="text-muted">Bagaimana cara mendiversifikasi portfolio untuk mengurangi risiko dengan target return 12%?</small>
                                </td>
                                <td><span class="badge badge-warning">Medium</span></td>
                                <td><span class="badge badge-success">Aktif</span></td>
                                <td>{{ date('d/m/Y', strtotime('-3 days')) }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-info" onclick="viewScenario(4)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editScenario(4)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteScenario(4)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr data-category="market_analysis" data-difficulty="hard">
                                <td>5</td>
                                <td><span class="badge badge-dark">Market Analysis</span></td>
                                <td>
                                    <strong>Analisis Tren Pasar Cryptocurrency</strong><br>
                                    <small class="text-muted">Menganalisis tren pasar crypto dan prediksi pergerakan harga untuk strategi trading...</small>
                                </td>
                                <td><span class="badge badge-danger">Hard</span></td>
                                <td><span class="badge badge-warning">Review</span></td>
                                <td>{{ date('d/m/Y', strtotime('-1 week')) }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-info" onclick="viewScenario(5)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editScenario(5)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteScenario(5)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">Menampilkan 5 dari 5 skenario</small>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm m-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADM-22: Modal untuk Melihat Opsi Jawaban Skenario -->
<div class="modal fade" id="scenarioDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">
                    <i class="fas fa-list-alt"></i> Detail Skenario & Opsi Jawaban
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="scenarioDetailContent">
                    <!-- Content akan diisi via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" onclick="editCurrentScenario()">
                    <i class="fas fa-edit"></i> Edit Skenario
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Tambah/Edit Skenario -->
<div class="modal fade" id="scenarioFormModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title">
                    <i class="fas fa-plus"></i> <span id="formModalTitle">Tambah Skenario Baru</span>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="scenarioForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="scenarioCategory">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="scenarioCategory" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="risk_assessment">Risk Assessment</option>
                                    <option value="financial_analysis">Financial Analysis</option>
                                    <option value="investment_strategy">Investment Strategy</option>
                                    <option value="portfolio_management">Portfolio Management</option>
                                    <option value="market_analysis">Market Analysis</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="scenarioDifficulty">Tingkat Kesulitan <span class="text-danger">*</span></label>
                                <select class="form-control" id="scenarioDifficulty" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="scenarioTitle">Judul Skenario <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="scenarioTitle" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="scenarioDescription">Deskripsi Skenario <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="scenarioDescription" rows="5" required></textarea>
                    </div>
                    
                    <h5>Opsi Jawaban:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="optionA">Opsi A <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="optionA" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="optionB">Opsi B <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="optionB" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="optionC">Opsi C <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="optionC" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="optionD">Opsi D <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="optionD" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="correctAnswer">Jawaban yang Benar <span class="text-danger">*</span></label>
                                <select class="form-control" id="correctAnswer" required>
                                    <option value="">Pilih Jawaban Benar</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="scenarioStatus">Status</label>
                                <select class="form-control" id="scenarioStatus">
                                    <option value="draft">Draft</option>
                                    <option value="active">Aktif</option>
                                    <option value="review">Review</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="explanation">Penjelasan Jawaban</label>
                        <textarea class="form-control" id="explanation" rows="3" placeholder="Penjelasan mengapa jawaban tersebut benar..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="saveScenario()">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Modal Fix -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
let currentScenarioId = null;

// Mock data for detailed scenario view
const scenarioDetails = {
    1: {
        title: "Analisis Risiko Investasi Saham",
        category: "Risk Assessment",
        difficulty: "Medium",
        description: "Anda diminta untuk menganalisis risiko investasi pada saham teknologi yang volatil. Perusahaan XYZ baru saja meluncurkan produk inovatif tetapi menghadapi persaingan ketat. Berdasarkan data historis dan kondisi pasar saat ini, bagaimana Anda menilai risiko investasi ini?",
        options: {
            A: "Investasi berisiko tinggi, sebaiknya hindari karena volatilitas tinggi",
            B: "Investasi dengan risiko sedang, alokasikan maksimal 10% dari portfolio",
            C: "Investasi menarik dengan potensi return tinggi, alokasikan 25% portfolio",
            D: "Tunggu hingga ada data kinerja produk yang lebih jelas"
        },
        correctAnswer: "B",
        explanation: "Investasi pada saham teknologi yang volatil memerlukan pendekatan hati-hati. Alokasi maksimal 10% dari portfolio memungkinkan investor untuk berpartisipasi dalam potensi pertumbuhan sambil membatasi risiko eksposur."
    },
    2: {
        title: "Evaluasi Kinerja Keuangan Perusahaan",
        category: "Financial Analysis", 
        difficulty: "Hard",
        description: "Berdasarkan laporan keuangan PT. ABC, analisis rasio keuangan dan berikan rekomendasi. ROE: 15%, Debt to Equity: 0.8, Current Ratio: 1.5, Quick Ratio: 1.2. Perusahaan beroperasi di sektor manufaktur dengan margin yang kompetitif.",
        options: {
            A: "Kinerja keuangan sangat baik, rekomendasikan untuk investasi jangka panjang",
            B: "Kinerja moderat dengan beberapa area yang perlu diperbaiki",  
            C: "Rasio hutang terlalu tinggi, tidak disarankan untuk investasi",
            D: "Likuiditas baik tetapi profitabilitas perlu ditingkatkan"
        },
        correctAnswer: "B",
        explanation: "ROE 15% menunjukkan profitabilitas yang baik, likuiditas cukup sehat, namun Debt to Equity 0.8 menunjukkan tingkat hutang yang perlu dimonitor. Secara keseluruhan kinerja moderat dengan ruang untuk perbaikan."
    }
};

function filterScenarios() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const difficultyFilter = document.getElementById('difficultyFilter').value;
    const rows = document.querySelectorAll('#scenariosTable tbody tr');
    
    rows.forEach(row => {
        const category = row.getAttribute('data-category');
        const difficulty = row.getAttribute('data-difficulty');
        
        let showRow = true;
        
        if (categoryFilter && category !== categoryFilter) {
            showRow = false;
        }
        
        if (difficultyFilter && difficulty !== difficultyFilter) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

function searchScenarios() {
    const searchTerm = document.getElementById('searchBox').value.toLowerCase();
    const rows = document.querySelectorAll('#scenariosTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function viewScenario(id) {
    currentScenarioId = id;
    const scenario = scenarioDetails[id];
    
    if (scenario) {
        const content = `
            <div class="scenario-detail">
                <h5>${scenario.title}</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Kategori:</strong> <span class="badge badge-primary">${scenario.category}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Tingkat:</strong> <span class="badge badge-warning">${scenario.difficulty}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Deskripsi Skenario:</strong>
                    <p class="mt-2">${scenario.description}</p>
                </div>
                
                <div class="mb-3">
                    <strong>Opsi Jawaban:</strong>
                    <div class="mt-2">
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <strong>A.</strong> ${scenario.options.A}
                            </div>
                        </div>
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <strong>B.</strong> ${scenario.options.B}
                            </div>
                        </div>
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <strong>C.</strong> ${scenario.options.C}
                            </div>
                        </div>
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <strong>D.</strong> ${scenario.options.D}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Jawaban Benar:</strong> <span class="badge badge-success">${scenario.correctAnswer}</span>
                </div>
                
                <div class="mb-3">
                    <strong>Penjelasan:</strong>
                    <p class="mt-2">${scenario.explanation}</p>
                </div>
            </div>
        `;
        
        document.getElementById('scenarioDetailContent').innerHTML = content;
        $('#scenarioDetailModal').modal('show');
    }
}

function editScenario(id) {
    currentScenarioId = id;
    document.getElementById('formModalTitle').textContent = 'Edit Skenario';
    
    // Populate form with existing data if editing
    if (scenarioDetails[id]) {
        const scenario = scenarioDetails[id];
        // Populate form fields here
    }
    
    $('#scenarioFormModal').modal('show');
}

function editCurrentScenario() {
    $('#scenarioDetailModal').modal('hide');
    setTimeout(() => {
        editScenario(currentScenarioId);
    }, 300);
}

function deleteScenario(id) {
    Swal.fire({
        title: 'Hapus Skenario?',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Skenario telah dihapus',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            // Remove row from table
            document.querySelector(`#scenariosTable tbody tr:nth-child(${id})`).remove();
        }
    });
}

function addScenario() {
    currentScenarioId = null;
    document.getElementById('formModalTitle').textContent = 'Tambah Skenario Baru';
    document.getElementById('scenarioForm').reset();
    $('#scenarioFormModal').modal('show');
}

function saveScenario() {
    const form = document.getElementById('scenarioForm');
    const formData = new FormData(form);
    
    // Validate required fields
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Sedang menyimpan skenario',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    }).then(() => {
        Swal.fire({
            title: 'Berhasil!',
            text: currentScenarioId ? 'Skenario berhasil diperbarui' : 'Skenario baru berhasil ditambahkan',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        $('#scenarioFormModal').modal('hide');
        // Refresh table here
    });
}

function refreshScenarios() {
    Swal.fire({
        title: 'Memuat Ulang...',
        text: 'Sedang memperbarui data skenario',
        icon: 'info',
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true
    }).then(() => {
        location.reload();
    });
}
</script>
@endpush

@push('styles')
<style>
.table td {
    vertical-align: middle;
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-size: 0.8em;
}

.modal-xl {
    max-width: 90%;
}

.scenario-detail .card {
    border-left: 3px solid #007bff;
}

.scenario-detail .card-body {
    background-color: #f8f9fa;
}

.table-responsive {
    border-radius: 0.25rem;
}

thead.thead-dark th {
    background-color: #343a40;
    border-color: #454d55;
}

.pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endpush