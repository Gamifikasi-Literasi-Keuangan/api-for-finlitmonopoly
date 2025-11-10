@extends('layouts.admin')

@section('title', 'Leaderboard')
@section('page-title', 'Leaderboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
    <li class="breadcrumb-item active">Leaderboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Papan Peringkat Pemain</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Peringkat</th>
                            <th>Nama Player</th>
                            <th>Skor Total</th>
                            <th>Cluster</th>
                            <th>Kategori</th>
                            <th>Badge</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><strong>Revan</strong></td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-success" style="width: 95%"></div>
                                </div>
                                <small>950 points</small>
                            </td>
                            <td><span class="badge bg-primary">Strategic Thinker</span></td>
                            <td>Risk, Chance, Scenario</td>
                            <td>🏆</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><strong>Arif</strong></td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-warning" style="width: 87%"></div>
                                </div>
                                <small>870 points</small>
                            </td>
                            <td><span class="badge bg-danger">Risk Taker</span></td>
                            <td>Quiz, Risk</td>
                            <td>🥈</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><strong>Revaldi Si Bodas</strong></td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-info" style="width: 75%"></div>
                                </div>
                                <small>750 points</small>
                            </td>
                            <td><span class="badge bg-info">Analytical</span></td>
                            <td>Scenario, Analysis</td>
                            <td>🥉</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td><strong>idham Khalid</strong></td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-secondary" style="width: 5%"></div>
                                </div>
                                <small>50 points</small>
                            </td>
                            <td><span class="badge bg-secondary">Conservative</span></td>
                            <td>Safety, Logic</td>
                            <td>⭐</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <p><i class="fas fa-clock"></i> Rankings updated hourly | Last update: {{ date('d M Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Info boxes -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Players</span>
                <span class="info-box-number">124</span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-trophy"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Winners</span>
                <span class="info-box-number">4</span>
            </div>
        </div>
    </div>
    
    <div class="clearfix hidden-md-up"></div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-star"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Points</span>
                <span class="info-box-number">3,220</span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-gamepad"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Games Played</span>
                <span class="info-box-number">89</span>
            </div>
        </div>
    </div>
</div>

<!-- Performance Chart -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Performance Overview</h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="areaChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    .progress {
        height: 10px;
    }
</style>
@endpush