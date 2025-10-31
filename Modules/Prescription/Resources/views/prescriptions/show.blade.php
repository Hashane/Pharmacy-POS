@extends('customer::layouts.app')

@section('title', 'Prescription Details')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-file-earmark-medical"></i> Prescription Details</h2>
            <a href="{{ route('customer.prescriptions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Prescriptions
            </a>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Status Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Status</h5>
                                <span class="badge 
                                    @if($prescription->status == 'pending') bg-warning
                                    @elseif($prescription->status == 'approved') bg-success
                                    @elseif($prescription->status == 'rejected') bg-danger
                                    @elseif($prescription->status == 'processing') bg-info
                                    @endif" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                    {{ ucfirst($prescription->status) }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <h5>Reference Number</h5>
                                <p class="mb-0"><strong>{{ $prescription->reference }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Your Notes -->
                @if($prescription->notes)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Your Notes</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $prescription->notes }}</p>
                        </div>
                    </div>
                @endif

                <!-- Admin Notes -->
                @if($prescription->admin_notes)
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Pharmacy Notes</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $prescription->admin_notes }}</p>
                        </div>
                    </div>
                @endif

                <!-- Uploaded Files -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Uploaded Files ({{ $prescription->files->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($prescription->files->count() > 0)
                            <div class="list-group">
                                @foreach($prescription->files as $file)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-{{ str_contains($file->mime_type, 'pdf') ? 'pdf' : 'image' }}"></i>
                                            <strong>{{ $file->file_name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ number_format($file->file_size / 1024 / 1024, 2) }} MB •
                                                Uploaded {{ $file->created_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="{{ Storage::url($file->file_path) }}" download class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No files attached to this prescription.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Timeline Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item active">
                                <i class="bi bi-upload"></i>
                                <div>
                                    <strong>Uploaded</strong>
                                    <small class="d-block text-muted">{{ $prescription->created_at->format('M d, Y - h:i A') }}</small>
                                </div>
                            </div>
                            @if($prescription->reviewed_at)
                                <div class="timeline-item active">
                                    <i class="bi bi-check-circle"></i>
                                    <div>
                                        <strong>Reviewed</strong>
                                        <small class="d-block text-muted">{{ $prescription->reviewed_at->format('M d, Y - h:i A') }}</small>
                                        @if($prescription->reviewer)
                                            <small class="d-block text-muted">By: {{ $prescription->reviewer->name }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-info-circle"></i> Status Information</h5>
                        @if($prescription->status == 'pending')
                            <p class="mb-0 small">
                                Your prescription is being reviewed by our pharmacy staff. You will be notified once it's processed.
                            </p>
                        @elseif($prescription->status == 'approved')
                            <p class="mb-0 small">
                                Your prescription has been approved. You can now add the prescribed items to your cart.
                            </p>
                        @elseif($prescription->status == 'rejected')
                            <p class="mb-0 small">
                                Your prescription could not be approved. Please check the pharmacy notes above for more information.
                            </p>
                        @elseif($prescription->status == 'processing')
                            <p class="mb-0 small">
                                Your prescription is currently being processed by our pharmacy team.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .timeline {
                position: relative;
            }
            .timeline-item {
                padding: 15px 0;
                padding-left: 45px;
                position: relative;
                color: #999;
            }
            .timeline-item i {
                position: absolute;
                left: 0;
                top: 15px;
                font-size: 1.5rem;
            }
            .timeline-item.active {
                color: #28a745;
            }
            .timeline-item.active i {
                color: #28a745;
            }
            .timeline-item:not(:last-child)::before {
                content: '';
                position: absolute;
                left: 11px;
                top: 40px;
                height: calc(100% - 25px);
                width: 2px;
                background: #ddd;
            }
            .timeline-item.active:not(:last-child)::before {
                background: #28a745;
            }
        </style>
    @endpush
@endsection