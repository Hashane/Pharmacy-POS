@extends('layouts.app')

@section('title', 'Prescriptions')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Prescriptions</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Customer Prescriptions</h4>
                            <div>
                                <a href="{{ route('admin.prescriptions.export') }}" class="btn btn-success">
                                    <i class="bi bi-download"></i> Export
                                </a>
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="mb-3">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.prescriptions.index') }}"
                                   class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                                    All
                                </a>
                                <a href="{{ route('admin.prescriptions.index', ['status' => 'pending']) }}"
                                   class="btn btn-sm btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">
                                    <i class="bi bi-clock"></i> Pending
                                </a>
                                <a href="{{ route('admin.prescriptions.index', ['status' => 'approved']) }}"
                                   class="btn btn-sm btn-outline-success {{ request('status') == 'approved' ? 'active' : '' }}">
                                    <i class="bi bi-check-circle"></i> Approved
                                </a>
                                <a href="{{ route('admin.prescriptions.index', ['status' => 'rejected']) }}"
                                   class="btn btn-sm btn-outline-danger {{ request('status') == 'rejected' ? 'active' : '' }}">
                                    <i class="bi bi-x-circle"></i> Rejected
                                </a>
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">Pending</h6>
                                                <h3 class="mb-0">{{ \Modules\Prescription\Entities\Prescription::where('status', 'pending')->count() }}</h3>
                                            </div>
                                            <i class="bi bi-clock" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">Approved</h6>
                                                <h3 class="mb-0">{{ \Modules\Prescription\Entities\Prescription::where('status', 'approved')->count() }}</h3>
                                            </div>
                                            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">Rejected</h6>
                                                <h3 class="mb-0">{{ \Modules\Prescription\Entities\Prescription::where('status', 'rejected')->count() }}</h3>
                                            </div>
                                            <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">Total</h6>
                                                <h3 class="mb-0">{{ \Modules\Prescription\Entities\Prescription::count() }}</h3>
                                            </div>
                                            <i class="bi bi-file-earmark-medical" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{ $dataTable->table() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    {{ $dataTable->scripts() }}
@endpush