@extends('layouts.admin')

@section('title', 'Kartu Risiko & Kesempatan')
@section('page-title', 'Manajemen Kartu Risiko & Kesempatan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/content/cards') }}">Manajemen Konten</a></li>
    <li class="breadcrumb-item active">Kartu Risiko & Kesempatan</li>
@endsection

@section('content')
<!-- ADM-23: Lihat Kartu Risiko dan Kesempatan -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-id-card"></i> Manajemen Kartu Risiko & Kesempatan
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-light" onclick="addCard()">
                        <i class="fas fa-plus"></i> Tambah Kartu
                    </button>
                    <button type="button" class="btn btn-outline-light" onclick="refreshCards()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter dan Tab -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" id="cardTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="risk-tab" data-toggle="tab" href="#risk" role="tab">
                                    <i class="fas fa-exclamation-triangle text-danger"></i> Kartu Risiko
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="chance-tab" data-toggle="tab" href="#chance" role="tab">
                                    <i class="fas fa-star text-warning"></i> Kartu Kesempatan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="all-tab" data-toggle="tab" href="#all" role="tab">
                                    <i class="fas fa-list"></i> Semua Kartu
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="tab-content" id="cardTabsContent">
                    <!-- Tab Kartu Risiko -->
                    <div class="tab-pane fade show active" id="risk" role="tabpanel">
                        <div class="row">
                            <!-- Risk Card 1 -->
                            <div class="col-md-4 mb-4">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-exclamation-triangle"></i> Krisis Ekonomi Global
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            <strong>Dampak:</strong> Portfolio value turun 15-25%<br>
                                            <strong>Durasi:</strong> 2-3 rounds<br>
                                            <strong>Kategori:</strong> Market Risk
                                        </p>
                                        <p class="card-text">
                                            Resesi global menyebabkan penurunan drastis pada semua aset berisiko. 
                                            Investor harus mempertimbangkan rebalancing portfolio.
                                        </p>
                                        <div class="card-footer bg-transparent">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> Dibuat: {{ date('d/m/Y', strtotime('-1 week')) }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="viewCard(1, 'risk')">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <button class="btn btn-warning" onclick="editCard(1, 'risk')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteCard(1, 'risk')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                        <span class="badge badge-success float-right">Aktif</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Risk Card 2 -->
                            <div class="col-md-4 mb-4">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-chart-line-down"></i> Volatilitas Tinggi
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            <strong>Dampak:</strong> Saham tech turun 10%<br>
                                            <strong>Durasi:</strong> 1 round<br>
                                            <strong>Kategori:</strong> Sector Risk
                                        </p>
                                        <p class="card-text">
                                            Sentimen negatif terhadap sektor teknologi menyebabkan sell-off massal. 
                                            Diversifikasi menjadi kunci.
                                        </p>
                                        <div class="card-footer bg-transparent">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> Dibuat: {{ date('d/m/Y', strtotime('-3 days')) }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="viewCard(2, 'risk')">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <button class="btn btn-warning" onclick="editCard(2, 'risk')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteCard(2, 'risk')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                        <span class="badge badge-success float-right">Aktif</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Risk Card 3 -->
                            <div class="col-md-4 mb-4">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-building"></i> Default Perusahaan
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            <strong>Dampak:</strong> Obligasi korporat -30%<br>
                                            <strong>Durasi:</strong> Permanen<br>
                                            <strong>Kategori:</strong> Credit Risk
                                        </p>
                                        <p class="card-text">
                                            Perusahaan besar mengalami kebangkrutan. Investor yang memegang obligasi 
                                            perusahaan tersebut mengalami kerugian signifikan.
                                        </p>
                                        <div class="card-footer bg-transparent">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> Dibuat: {{ date('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="viewCard(3, 'risk')">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <button class="btn btn-warning" onclick="editCard(3, 'risk')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteCard(3, 'risk')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                        <span class="badge badge-warning float-right">Draft</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Kartu Kesempatan -->
                    <div class="tab-pane fade" id="chance" role="tabpanel">
                        <div class="row">
                            <!-- Chance Card 1 -->
                            <div class="col-md-4 mb-4">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-chart-line"></i> Bull Market Rally
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            <strong>Keuntungan:</strong> Portfolio +20%<br>
                                            <strong>Durasi:</strong> 2 rounds<br>
                                            <strong>Kategori:</strong> Market Opportunity
                                        </p>
                                        <p class="card-text">
                                            Pasar mengalami tren naik yang kuat. Semua aset berisiko mengalami 
                                            apresiasi nilai yang signifikan. Momentum positif berlanjut.
                                        </p>
                                        <div class="card-footer bg-transparent">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> Dibuat: {{ date('d/m/Y', strtotime('-5 days')) }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="viewCard(1, 'chance')">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <button class="btn btn-warning" onclick="editCard(1, 'chance')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteCard(1, 'chance')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                        <span class="badge badge-success float-right">Aktif</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Chance Card 2 -->
                            <div class="col-md-4 mb-4">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-lightbulb"></i> Inovasi Teknologi
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            <strong>Keuntungan:</strong> Tech stocks +15%<br>
                                            <strong>Durasi:</strong> 3 rounds<br>
                                            <strong>Kategori:</strong> Innovation Boom
                                        </p>
                                        <p class="card-text">
                                            Breakthrough teknologi AI menggerakkan sektor teknologi. Perusahaan 
                                            tech mengalami pertumbuhan revenue yang luar biasa.
                                        </p>
                                        <div class="card-footer bg-transparent">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> Dibuat: {{ date('d/m/Y', strtotime('-2 days')) }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="viewCard(2, 'chance')">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <button class="btn btn-warning" onclick="editCard(2, 'chance')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteCard(2, 'chance')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                        <span class="badge badge-success float-right">Aktif</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Chance Card 3 -->
                            <div class="col-md-4 mb-4">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-coins"></i> Dividend Surprise
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            <strong>Keuntungan:</strong> Extra dividend 5%<br>
                                            <strong>Durasi:</strong> 1 round<br>
                                            <strong>Kategori:</strong> Income Boost
                                        </p>
                                        <p class="card-text">
                                            Perusahaan blue-chip mengumumkan dividend khusus karena kinerja 
                                            keuangan yang luar biasa. Investor mendapat bonus income.
                                        </p>
                                        <div class="card-footer bg-transparent">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> Dibuat: {{ date('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="viewCard(3, 'chance')">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <button class="btn btn-warning" onclick="editCard(3, 'chance')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteCard(3, 'chance')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                        <span class="badge badge-success float-right">Aktif</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Semua Kartu -->
                    <div class="tab-pane fade" id="all" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Jenis</th>
                                        <th>Judul</th>
                                        <th>Kategori</th>
                                        <th>Dampak</th>
                                        <th>Status</th>
                                        <th>Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><span class="badge badge-danger">Risk</span></td>
                                        <td>Krisis Ekonomi Global</td>
                                        <td>Market Risk</td>
                                        <td>-15% to -25%</td>
                                        <td><span class="badge badge-success">Aktif</span></td>
                                        <td>{{ date('d/m/Y', strtotime('-1 week')) }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" onclick="viewCard(1, 'risk')"><i class="fas fa-eye"></i></button>
                                                <button class="btn btn-warning" onclick="editCard(1, 'risk')"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger" onclick="deleteCard(1, 'risk')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><span class="badge badge-success">Chance</span></td>
                                        <td>Bull Market Rally</td>
                                        <td>Market Opportunity</td>
                                        <td>+20%</td>
                                        <td><span class="badge badge-success">Aktif</span></td>
                                        <td>{{ date('d/m/Y', strtotime('-5 days')) }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" onclick="viewCard(1, 'chance')"><i class="fas fa-eye"></i></button>
                                                <button class="btn btn-warning" onclick="editCard(1, 'chance')"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger" onclick="deleteCard(1, 'chance')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><span class="badge badge-danger">Risk</span></td>
                                        <td>Volatilitas Tinggi</td>
                                        <td>Sector Risk</td>
                                        <td>-10%</td>
                                        <td><span class="badge badge-success">Aktif</span></td>
                                        <td>{{ date('d/m/Y', strtotime('-3 days')) }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" onclick="viewCard(2, 'risk')"><i class="fas fa-eye"></i></button>
                                                <button class="btn btn-warning" onclick="editCard(2, 'risk')"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger" onclick="deleteCard(2, 'risk')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Detail Kartu -->
<div class="modal fade" id="cardDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" id="cardDetailHeader">
                <h4 class="modal-title">
                    <i class="fas fa-id-card"></i> Detail Kartu
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="cardDetailContent">
                <!-- Content akan diisi via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" onclick="editCurrentCard()">
                    <i class="fas fa-edit"></i> Edit Kartu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Tambah/Edit Kartu -->
<div class="modal fade" id="cardFormModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">
                    <i class="fas fa-plus"></i> <span id="cardFormTitle">Tambah Kartu Baru</span>
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="cardForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cardType">Jenis Kartu <span class="text-danger">*</span></label>
                                <select class="form-control" id="cardType" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="risk">Kartu Risiko</option>
                                    <option value="chance">Kartu Kesempatan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cardCategory">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="cardCategory" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="market_risk">Market Risk</option>
                                    <option value="sector_risk">Sector Risk</option>
                                    <option value="credit_risk">Credit Risk</option>
                                    <option value="market_opportunity">Market Opportunity</option>
                                    <option value="innovation_boom">Innovation Boom</option>
                                    <option value="income_boost">Income Boost</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="cardTitle">Judul Kartu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cardTitle" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cardDescription">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cardDescription" rows="4" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cardImpact">Dampak (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="cardImpact" step="0.1" required>
                                <small class="form-text text-muted">Gunakan nilai negatif untuk risiko</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cardDuration">Durasi (rounds)</label>
                                <input type="number" class="form-control" id="cardDuration" min="1" value="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cardStatus">Status</label>
                                <select class="form-control" id="cardStatus">
                                    <option value="active">Aktif</option>
                                    <option value="draft">Draft</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="cardTrigger">Kondisi Pemicu</label>
                        <textarea class="form-control" id="cardTrigger" rows="2" placeholder="Kondisi atau situasi yang memicu kartu ini..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="saveCard()">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
let currentCardId = null;
let currentCardType = null;

// Mock data untuk detail kartu
const cardDetails = {
    risk: {
        1: {
            title: "Krisis Ekonomi Global",
            category: "Market Risk",
            description: "Resesi global menyebabkan penurunan drastis pada semua aset berisiko. Investor harus mempertimbangkan rebalancing portfolio.",
            impact: -20,
            duration: 3,
            trigger: "Indikator ekonomi menunjukkan kontraksi GDP di beberapa negara besar"
        },
        2: {
            title: "Volatilitas Tinggi", 
            category: "Sector Risk",
            description: "Sentimen negatif terhadap sektor teknologi menyebabkan sell-off massal. Diversifikasi menjadi kunci.",
            impact: -10,
            duration: 1,
            trigger: "Laporan earnings tech companies dibawah ekspektasi"
        }
    },
    chance: {
        1: {
            title: "Bull Market Rally",
            category: "Market Opportunity", 
            description: "Pasar mengalami tren naik yang kuat. Semua aset berisiko mengalami apresiasi nilai yang signifikan.",
            impact: 20,
            duration: 2,
            trigger: "Kebijakan moneter ekspansif dan optimisme ekonomi"
        },
        2: {
            title: "Inovasi Teknologi",
            category: "Innovation Boom",
            description: "Breakthrough teknologi AI menggerakkan sektor teknologi. Perusahaan tech mengalami pertumbuhan revenue luar biasa.",
            impact: 15,
            duration: 3,
            trigger: "Peluncuran teknologi disruptif baru"
        }
    }
};

function viewCard(id, type) {
    currentCardId = id;
    currentCardType = type;
    
    const card = cardDetails[type][id];
    if (card) {
        const headerClass = type === 'risk' ? 'bg-danger' : 'bg-success';
        const icon = type === 'risk' ? 'fas fa-exclamation-triangle' : 'fas fa-star';
        
        document.getElementById('cardDetailHeader').className = `modal-header ${headerClass} text-white`;
        
        const content = `
            <div class="card-detail">
                <h5><i class="${icon}"></i> ${card.title}</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Jenis:</strong> <span class="badge badge-${type === 'risk' ? 'danger' : 'success'}">${type === 'risk' ? 'Kartu Risiko' : 'Kartu Kesempatan'}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Kategori:</strong> <span class="badge badge-info">${card.category}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Deskripsi:</strong>
                    <p class="mt-2">${card.description}</p>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Dampak:</strong>
                        <span class="badge badge-${card.impact < 0 ? 'danger' : 'success'} badge-lg">
                            ${card.impact > 0 ? '+' : ''}${card.impact}%
                        </span>
                    </div>
                    <div class="col-md-4">
                        <strong>Durasi:</strong>
                        <span class="badge badge-info">${card.duration} rounds</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Status:</strong>
                        <span class="badge badge-success">Aktif</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Kondisi Pemicu:</strong>
                    <p class="mt-2 text-muted">${card.trigger}</p>
                </div>
            </div>
        `;
        
        document.getElementById('cardDetailContent').innerHTML = content;
        $('#cardDetailModal').modal('show');
    }
}

function editCard(id, type) {
    currentCardId = id;
    currentCardType = type;
    document.getElementById('cardFormTitle').textContent = 'Edit Kartu';
    
    // Populate form dengan data existing
    const card = cardDetails[type][id];
    if (card) {
        document.getElementById('cardType').value = type;
        document.getElementById('cardTitle').value = card.title;
        document.getElementById('cardDescription').value = card.description;
        document.getElementById('cardImpact').value = card.impact;
        document.getElementById('cardDuration').value = card.duration;
        document.getElementById('cardTrigger').value = card.trigger;
    }
    
    $('#cardFormModal').modal('show');
}

function editCurrentCard() {
    $('#cardDetailModal').modal('hide');
    setTimeout(() => {
        editCard(currentCardId, currentCardType);
    }, 300);
}

function deleteCard(id, type) {
    Swal.fire({
        title: 'Hapus Kartu?',
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
                text: 'Kartu telah dihapus',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            // Remove card from view
            location.reload();
        }
    });
}

function addCard() {
    currentCardId = null;
    currentCardType = null;
    document.getElementById('cardFormTitle').textContent = 'Tambah Kartu Baru';
    document.getElementById('cardForm').reset();
    $('#cardFormModal').modal('show');
}

function saveCard() {
    const form = document.getElementById('cardForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Sedang menyimpan kartu',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    }).then(() => {
        Swal.fire({
            title: 'Berhasil!',
            text: currentCardId ? 'Kartu berhasil diperbarui' : 'Kartu baru berhasil ditambahkan',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        $('#cardFormModal').modal('hide');
        // Refresh view
    });
}

function refreshCards() {
    Swal.fire({
        title: 'Memuat Ulang...',
        text: 'Sedang memperbarui data kartu',
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
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card-header h5 {
    margin-bottom: 0;
}

.card.border-danger:hover {
    box-shadow: 0 8px 25px rgba(220,53,69,0.3);
}

.card.border-success:hover {
    box-shadow: 0 8px 25px rgba(40,167,69,0.3);
}

.badge-lg {
    font-size: 1em;
    padding: 0.5em 0.75em;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.btn-group-sm > .btn {
    margin-right: 2px;
}

.table td {
    vertical-align: middle;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
@endpush