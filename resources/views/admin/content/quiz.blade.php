@extends('layouts.admin')

@section('title', 'Kuis & Opsi')
@section('page-title', 'Manajemen Kuis & Opsi Jawaban')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/content/quiz') }}">Manajemen Konten</a></li>
    <li class="breadcrumb-item active">Kuis & Opsi</li>
@endsection

@section('content')
<!-- ADM-24: Lihat Kuis & Opsi -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title">
                    <i class="fas fa-question-circle"></i> Manajemen Kuis & Opsi Jawaban
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-light" onclick="addQuiz()">
                        <i class="fas fa-plus"></i> Tambah Kuis
                    </button>
                    <button type="button" class="btn btn-outline-light" onclick="importQuiz()">
                        <i class="fas fa-upload"></i> Import
                    </button>
                    <button type="button" class="btn btn-outline-light" onclick="refreshQuiz()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter dan Search -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="categoryFilter">Filter Kategori:</label>
                            <select class="form-control" id="categoryFilter" onchange="filterQuiz()">
                                <option value="">Semua Kategori</option>
                                <option value="general_knowledge">General Knowledge</option>
                                <option value="financial_literacy">Financial Literacy</option>
                                <option value="risk_management">Risk Management</option>
                                <option value="investment_basics">Investment Basics</option>
                                <option value="market_analysis">Market Analysis</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="difficultyFilter">Filter Tingkat:</label>
                            <select class="form-control" id="difficultyFilter" onchange="filterQuiz()">
                                <option value="">Semua Tingkat</option>
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="typeFilter">Filter Tipe:</label>
                            <select class="form-control" id="typeFilter" onchange="filterQuiz()">
                                <option value="">Semua Tipe</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="fill_blank">Fill in the Blank</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="searchBox">Pencarian:</label>
                            <input type="text" class="form-control" id="searchBox" placeholder="Cari kuis..." onkeyup="searchQuiz()">
                        </div>
                    </div>
                </div>
                
                <!-- Quiz Cards Grid -->
                <div class="row" id="quizGrid">
                    <!-- Quiz Card 1 -->
                    <div class="col-md-6 col-lg-4 mb-4" data-category="financial_literacy" data-difficulty="easy" data-type="multiple_choice">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-question"></i> Dasar-dasar Investasi
                                </h6>
                                <span class="badge badge-light">Multiple Choice</span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Pertanyaan:</strong><br>
                                    Apa yang dimaksud dengan diversifikasi dalam investasi?
                                </p>
                                <div class="quiz-meta">
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> Financial Literacy<br>
                                        <i class="fas fa-signal"></i> Easy<br>
                                        <i class="fas fa-clock"></i> 30 detik<br>
                                        <i class="fas fa-star"></i> 10 poin
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-success">Aktif</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" onclick="viewQuiz(1)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning" onclick="editQuiz(1)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteQuiz(1)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quiz Card 2 -->
                    <div class="col-md-6 col-lg-4 mb-4" data-category="risk_management" data-difficulty="medium" data-type="multiple_choice">
                        <div class="card border-warning h-100">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-question"></i> Analisis Risiko
                                </h6>
                                <span class="badge badge-dark">Multiple Choice</span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Pertanyaan:</strong><br>
                                    Bagaimana cara menghitung Value at Risk (VaR) untuk portfolio?
                                </p>
                                <div class="quiz-meta">
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> Risk Management<br>
                                        <i class="fas fa-signal"></i> Medium<br>
                                        <i class="fas fa-clock"></i> 45 detik<br>
                                        <i class="fas fa-star"></i> 15 poin
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-success">Aktif</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" onclick="viewQuiz(2)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning" onclick="editQuiz(2)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteQuiz(2)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quiz Card 3 -->
                    <div class="col-md-6 col-lg-4 mb-4" data-category="market_analysis" data-difficulty="hard" data-type="multiple_choice">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-question"></i> Analisis Teknikal
                                </h6>
                                <span class="badge badge-light">Multiple Choice</span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Pertanyaan:</strong><br>
                                    Interpretasi pattern Head and Shoulders dalam analisis teknikal adalah?
                                </p>
                                <div class="quiz-meta">
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> Market Analysis<br>
                                        <i class="fas fa-signal"></i> Hard<br>
                                        <i class="fas fa-clock"></i> 60 detik<br>
                                        <i class="fas fa-star"></i> 20 poin
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-warning">Review</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" onclick="viewQuiz(3)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning" onclick="editQuiz(3)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteQuiz(3)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quiz Card 4 - True/False -->
                    <div class="col-md-6 col-lg-4 mb-4" data-category="investment_basics" data-difficulty="easy" data-type="true_false">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-question"></i> Konsep Investasi
                                </h6>
                                <span class="badge badge-light">True/False</span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Pernyataan:</strong><br>
                                    Obligasi pemerintah selalu lebih aman daripada saham blue-chip.
                                </p>
                                <div class="quiz-meta">
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> Investment Basics<br>
                                        <i class="fas fa-signal"></i> Easy<br>
                                        <i class="fas fa-clock"></i> 20 detik<br>
                                        <i class="fas fa-star"></i> 8 poin
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-success">Aktif</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" onclick="viewQuiz(4)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning" onclick="editQuiz(4)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteQuiz(4)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quiz Card 5 - Fill in the Blank -->
                    <div class="col-md-6 col-lg-4 mb-4" data-category="general_knowledge" data-difficulty="medium" data-type="fill_blank">
                        <div class="card border-info h-100">
                            <div class="card-header bg-info text-white">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-question"></i> Terminologi Keuangan
                                </h6>
                                <span class="badge badge-light">Fill in the Blank</span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Lengkapi:</strong><br>
                                    Rasio yang mengukur kemampuan perusahaan membayar hutang jangka pendek adalah _____ ratio.
                                </p>
                                <div class="quiz-meta">
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> General Knowledge<br>
                                        <i class="fas fa-signal"></i> Medium<br>
                                        <i class="fas fa-clock"></i> 30 detik<br>
                                        <i class="fas fa-star"></i> 12 poin
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-secondary">Draft</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" onclick="viewQuiz(5)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning" onclick="editQuiz(5)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteQuiz(5)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quiz Card 6 -->
                    <div class="col-md-6 col-lg-4 mb-4" data-category="financial_literacy" data-difficulty="medium" data-type="multiple_choice">
                        <div class="card border-secondary h-100">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-question"></i> Manajemen Portfolio
                                </h6>
                                <span class="badge badge-light">Multiple Choice</span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Pertanyaan:</strong><br>
                                    Strategi rebalancing portfolio sebaiknya dilakukan...
                                </p>
                                <div class="quiz-meta">
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> Financial Literacy<br>
                                        <i class="fas fa-signal"></i> Medium<br>
                                        <i class="fas fa-clock"></i> 40 detik<br>
                                        <i class="fas fa-star"></i> 15 poin
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-success">Aktif</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" onclick="viewQuiz(6)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning" onclick="editQuiz(6)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteQuiz(6)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">Menampilkan 6 dari 24 kuis</small>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm m-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">4</a></li>
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

<!-- Modal untuk Detail Quiz dan Opsi -->
<div class="modal fade" id="quizDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">
                    <i class="fas fa-question-circle"></i> Detail Kuis & Opsi Jawaban
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="quizDetailContent">
                <!-- Content akan diisi via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" onclick="editCurrentQuiz()">
                    <i class="fas fa-edit"></i> Edit Kuis
                </button>
                <button type="button" class="btn btn-success" onclick="duplicateQuiz()">
                    <i class="fas fa-copy"></i> Duplikasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Tambah/Edit Quiz -->
<div class="modal fade" id="quizFormModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title">
                    <i class="fas fa-plus"></i> <span id="quizFormTitle">Tambah Kuis Baru</span>
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="quizForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quizCategory">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="quizCategory" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="general_knowledge">General Knowledge</option>
                                    <option value="financial_literacy">Financial Literacy</option>
                                    <option value="risk_management">Risk Management</option>
                                    <option value="investment_basics">Investment Basics</option>
                                    <option value="market_analysis">Market Analysis</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quizDifficulty">Tingkat Kesulitan <span class="text-danger">*</span></label>
                                <select class="form-control" id="quizDifficulty" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="easy">Easy (5-10 poin)</option>
                                    <option value="medium">Medium (10-15 poin)</option>
                                    <option value="hard">Hard (15-25 poin)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quizType">Tipe Kuis <span class="text-danger">*</span></label>
                                <select class="form-control" id="quizType" onchange="toggleQuizOptions()" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="fill_blank">Fill in the Blank</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="quizTitle">Judul/Topik Kuis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quizTitle" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quizQuestion">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="quizQuestion" rows="3" required></textarea>
                    </div>
                    
                    <!-- Multiple Choice Options -->
                    <div id="multipleChoiceOptions" style="display: none;">
                        <h6>Opsi Jawaban:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="optionA">A. <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="optionA" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="optionB">B. <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="optionB" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="optionC">C. <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="optionC" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="optionD">D. <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="optionD" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="correctAnswer">Jawaban yang Benar <span class="text-danger">*</span></label>
                            <select class="form-control" id="correctAnswer">
                                <option value="">Pilih Jawaban Benar</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- True/False Options -->
                    <div id="trueFalseOptions" style="display: none;">
                        <div class="form-group">
                            <label for="trueFalseAnswer">Jawaban yang Benar <span class="text-danger">*</span></label>
                            <select class="form-control" id="trueFalseAnswer">
                                <option value="">Pilih Jawaban</option>
                                <option value="true">True (Benar)</option>
                                <option value="false">False (Salah)</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Fill in the Blank Options -->
                    <div id="fillBlankOptions" style="display: none;">
                        <div class="form-group">
                            <label for="correctFillAnswer">Jawaban yang Benar <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="correctFillAnswer" placeholder="Masukkan jawaban yang benar">
                            <small class="form-text text-muted">Untuk multiple jawaban benar, pisahkan dengan koma (,)</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="timeLimit">Batas Waktu (detik)</label>
                                <input type="number" class="form-control" id="timeLimit" min="10" max="300" value="30">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="points">Poin <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="points" min="1" max="50" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quizStatus">Status</label>
                                <select class="form-control" id="quizStatus">
                                    <option value="active">Aktif</option>
                                    <option value="draft">Draft</option>
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
                    
                    <div class="form-group">
                        <label for="tags">Tags (opsional)</label>
                        <input type="text" class="form-control" id="tags" placeholder="pisahkan, dengan, koma">
                        <small class="form-text text-muted">Contoh: investasi, saham, obligasi</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info" onclick="previewQuiz()">
                    <i class="fas fa-eye"></i> Preview
                </button>
                <button type="button" class="btn btn-success" onclick="saveQuiz()">
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
let currentQuizId = null;

// Mock data untuk detail quiz
const quizDetails = {
    1: {
        title: "Dasar-dasar Investasi",
        category: "Financial Literacy",
        difficulty: "Easy",
        type: "multiple_choice",
        question: "Apa yang dimaksud dengan diversifikasi dalam investasi?",
        options: {
            A: "Membeli satu jenis investasi dalam jumlah besar",
            B: "Menyebar investasi pada berbagai jenis aset untuk mengurangi risiko",
            C: "Menjual semua investasi saat harga naik",
            D: "Hanya berinvestasi pada saham blue-chip"
        },
        correctAnswer: "B",
        timeLimit: 30,
        points: 10,
        explanation: "Diversifikasi adalah strategi menyebar investasi pada berbagai jenis aset (saham, obligasi, properti, dll) untuk mengurangi risiko kerugian total jika satu jenis investasi mengalami penurunan nilai."
    },
    4: {
        title: "Konsep Investasi",
        category: "Investment Basics", 
        difficulty: "Easy",
        type: "true_false",
        question: "Obligasi pemerintah selalu lebih aman daripada saham blue-chip.",
        correctAnswer: "false",
        timeLimit: 20,
        points: 8,
        explanation: "Meskipun obligasi pemerintah umumnya dianggap lebih aman, dalam kondisi tertentu saham blue-chip dari perusahaan stabil dapat memberikan keamanan yang sebanding dengan potensi return yang lebih tinggi."
    }
};

function filterQuiz() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const difficultyFilter = document.getElementById('difficultyFilter').value; 
    const typeFilter = document.getElementById('typeFilter').value;
    const cards = document.querySelectorAll('#quizGrid > div');
    
    cards.forEach(card => {
        const category = card.getAttribute('data-category');
        const difficulty = card.getAttribute('data-difficulty');
        const type = card.getAttribute('data-type');
        
        let showCard = true;
        
        if (categoryFilter && category !== categoryFilter) showCard = false;
        if (difficultyFilter && difficulty !== difficultyFilter) showCard = false;
        if (typeFilter && type !== typeFilter) showCard = false;
        
        card.style.display = showCard ? '' : 'none';
    });
}

function searchQuiz() {
    const searchTerm = document.getElementById('searchBox').value.toLowerCase();
    const cards = document.querySelectorAll('#quizGrid > div');
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function viewQuiz(id) {
    currentQuizId = id;
    const quiz = quizDetails[id];
    
    if (quiz) {
        let optionsHtml = '';
        
        if (quiz.type === 'multiple_choice') {
            optionsHtml = `
                <div class="mb-3">
                    <strong>Opsi Jawaban:</strong>
                    <div class="mt-2">
                        <div class="card mb-2 ${quiz.correctAnswer === 'A' ? 'border-success' : ''}">
                            <div class="card-body py-2">
                                <strong>A.</strong> ${quiz.options.A}
                                ${quiz.correctAnswer === 'A' ? '<span class="badge badge-success float-right">Benar</span>' : ''}
                            </div>
                        </div>
                        <div class="card mb-2 ${quiz.correctAnswer === 'B' ? 'border-success' : ''}">
                            <div class="card-body py-2">
                                <strong>B.</strong> ${quiz.options.B}
                                ${quiz.correctAnswer === 'B' ? '<span class="badge badge-success float-right">Benar</span>' : ''}
                            </div>
                        </div>
                        <div class="card mb-2 ${quiz.correctAnswer === 'C' ? 'border-success' : ''}">
                            <div class="card-body py-2">
                                <strong>C.</strong> ${quiz.options.C}
                                ${quiz.correctAnswer === 'C' ? '<span class="badge badge-success float-right">Benar</span>' : ''}
                            </div>
                        </div>
                        <div class="card mb-2 ${quiz.correctAnswer === 'D' ? 'border-success' : ''}">
                            <div class="card-body py-2">
                                <strong>D.</strong> ${quiz.options.D}
                                ${quiz.correctAnswer === 'D' ? '<span class="badge badge-success float-right">Benar</span>' : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else if (quiz.type === 'true_false') {
            optionsHtml = `
                <div class="mb-3">
                    <strong>Opsi Jawaban:</strong>
                    <div class="mt-2">
                        <div class="card mb-2 ${quiz.correctAnswer === 'true' ? 'border-success' : ''}">
                            <div class="card-body py-2">
                                <strong>True (Benar)</strong>
                                ${quiz.correctAnswer === 'true' ? '<span class="badge badge-success float-right">Benar</span>' : ''}
                            </div>
                        </div>
                        <div class="card mb-2 ${quiz.correctAnswer === 'false' ? 'border-success' : ''}">
                            <div class="card-body py-2">
                                <strong>False (Salah)</strong>
                                ${quiz.correctAnswer === 'false' ? '<span class="badge badge-success float-right">Benar</span>' : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        const content = `
            <div class="quiz-detail">
                <h5>${quiz.title}</h5>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Kategori:</strong><br>
                        <span class="badge badge-primary">${quiz.category}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Tingkat:</strong><br>
                        <span class="badge badge-${quiz.difficulty === 'easy' ? 'success' : quiz.difficulty === 'medium' ? 'warning' : 'danger'}">${quiz.difficulty}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Tipe:</strong><br>
                        <span class="badge badge-info">${quiz.type.replace('_', ' ')}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Poin:</strong><br>
                        <span class="badge badge-dark">${quiz.points} poin</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Pertanyaan:</strong>
                    <p class="mt-2 p-3 bg-light border-left border-primary">${quiz.question}</p>
                </div>
                
                ${optionsHtml}
                
                <div class="mb-3">
                    <strong>Batas Waktu:</strong> ${quiz.timeLimit} detik
                </div>
                
                <div class="mb-3">
                    <strong>Penjelasan:</strong>
                    <p class="mt-2">${quiz.explanation}</p>
                </div>
            </div>
        `;
        
        document.getElementById('quizDetailContent').innerHTML = content;
        $('#quizDetailModal').modal('show');
    }
}

function editQuiz(id) {
    currentQuizId = id;
    document.getElementById('quizFormTitle').textContent = 'Edit Kuis';
    
    // Populate form dengan data existing
    if (quizDetails[id]) {
        const quiz = quizDetails[id];
        document.getElementById('quizCategory').value = quiz.category.toLowerCase().replace(' ', '_');
        document.getElementById('quizDifficulty').value = quiz.difficulty.toLowerCase();
        document.getElementById('quizType').value = quiz.type;
        document.getElementById('quizTitle').value = quiz.title;
        document.getElementById('quizQuestion').value = quiz.question;
        
        toggleQuizOptions();
        
        if (quiz.type === 'multiple_choice') {
            document.getElementById('optionA').value = quiz.options.A;
            document.getElementById('optionB').value = quiz.options.B;
            document.getElementById('optionC').value = quiz.options.C;
            document.getElementById('optionD').value = quiz.options.D;
            document.getElementById('correctAnswer').value = quiz.correctAnswer;
        }
        
        document.getElementById('timeLimit').value = quiz.timeLimit;
        document.getElementById('points').value = quiz.points;
        document.getElementById('explanation').value = quiz.explanation;
    }
    
    $('#quizFormModal').modal('show');
}

function editCurrentQuiz() {
    $('#quizDetailModal').modal('hide');
    setTimeout(() => {
        editQuiz(currentQuizId);
    }, 300);
}

function deleteQuiz(id) {
    Swal.fire({
        title: 'Hapus Kuis?',
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
                text: 'Kuis telah dihapus',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            // Remove card
            location.reload();
        }
    });
}

function addQuiz() {
    currentQuizId = null;
    document.getElementById('quizFormTitle').textContent = 'Tambah Kuis Baru';
    document.getElementById('quizForm').reset();
    toggleQuizOptions();
    $('#quizFormModal').modal('show');
}

function toggleQuizOptions() {
    const quizType = document.getElementById('quizType').value;
    const multipleChoice = document.getElementById('multipleChoiceOptions');
    const trueFalse = document.getElementById('trueFalseOptions');
    const fillBlank = document.getElementById('fillBlankOptions');
    
    // Hide all options first
    multipleChoice.style.display = 'none';
    trueFalse.style.display = 'none';
    fillBlank.style.display = 'none';
    
    // Show relevant options
    if (quizType === 'multiple_choice') {
        multipleChoice.style.display = 'block';
    } else if (quizType === 'true_false') {
        trueFalse.style.display = 'block';
    } else if (quizType === 'fill_blank') {
        fillBlank.style.display = 'block';
    }
    
    // Auto-set points based on difficulty
    const difficulty = document.getElementById('quizDifficulty').value;
    const pointsInput = document.getElementById('points');
    
    if (difficulty === 'easy') {
        pointsInput.value = quizType === 'true_false' ? 8 : 10;
    } else if (difficulty === 'medium') {
        pointsInput.value = quizType === 'fill_blank' ? 12 : 15;
    } else if (difficulty === 'hard') {
        pointsInput.value = 20;
    }
}

function previewQuiz() {
    // Implementasi preview kuis
    Swal.fire({
        title: 'Preview Kuis',
        text: 'Fitur preview akan menampilkan kuis seperti yang dilihat pemain',
        icon: 'info'
    });
}

function saveQuiz() {
    const form = document.getElementById('quizForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Sedang menyimpan kuis',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    }).then(() => {
        Swal.fire({
            title: 'Berhasil!',
            text: currentQuizId ? 'Kuis berhasil diperbarui' : 'Kuis baru berhasil ditambahkan',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        $('#quizFormModal').modal('hide');
    });
}

function duplicateQuiz() {
    $('#quizDetailModal').modal('hide');
    
    Swal.fire({
        title: 'Duplikasi Kuis',
        text: 'Kuis akan diduplikasi dengan status Draft',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Duplikasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Kuis berhasil diduplikasi',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

function importQuiz() {
    Swal.fire({
        title: 'Import Kuis',
        html: `
            <div class="text-left">
                <p>Upload file Excel (.xlsx) atau CSV yang berisi kuis.</p>
                <input type="file" class="form-control" accept=".xlsx,.csv" id="importFile">
                <small class="text-muted mt-2">Format: Kategori, Tingkat, Tipe, Pertanyaan, Opsi A-D, Jawaban Benar</small>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Batal'
    });
}

function refreshQuiz() {
    Swal.fire({
        title: 'Memuat Ulang...',
        text: 'Sedang memperbarui data kuis',
        icon: 'info',
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true
    }).then(() => {
        location.reload();
    });
}

// Auto-set points when difficulty changes
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('quizDifficulty').addEventListener('change', toggleQuizOptions);
});
</script>
@endpush

@push('styles')
<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.card.border-primary:hover { box-shadow: 0 6px 20px rgba(0,123,255,0.3); }
.card.border-success:hover { box-shadow: 0 6px 20px rgba(40,167,69,0.3); }
.card.border-warning:hover { box-shadow: 0 6px 20px rgba(255,193,7,0.3); }
.card.border-danger:hover { box-shadow: 0 6px 20px rgba(220,53,69,0.3); }
.card.border-info:hover { box-shadow: 0 6px 20px rgba(23,162,184,0.3); }
.card.border-secondary:hover { box-shadow: 0 6px 20px rgba(108,117,125,0.3); }

.quiz-meta {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.quiz-meta i {
    width: 15px;
    margin-right: 5px;
}

.btn-group-sm > .btn {
    margin-right: 2px;
}

.modal-xl {
    max-width: 95%;
}

.quiz-detail .border-left {
    border-left: 4px solid #007bff !important;
}

.quiz-detail .card.border-success {
    border-left: 4px solid #28a745 !important;
}

.badge {
    font-size: 0.75em;
}

.card-footer {
    background-color: rgba(0,0,0,0.03);
}

.pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.h-100 {
    height: 100% !important;
}

#quizGrid .col-md-6,
#quizGrid .col-lg-4 {
    margin-bottom: 1rem;
}
</style>
@endpush