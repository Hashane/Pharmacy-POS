@extends('layouts.app')

@section('title', 'Prescription Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.prescriptions.index') }}">Prescriptions</a></li>
        <li class="breadcrumb-item active">{{ $prescription->reference }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <!-- Prescription Status Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-medical"></i> Prescription {{ $prescription->reference }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Current Status:</label>
                                <div>
                                    <span class="badge 
                                        @if($prescription->status == 'pending') bg-warning
                                        @elseif($prescription->status == 'approved') bg-success
                                        @elseif($prescription->status == 'rejected') bg-danger
                                        @endif" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                        {{ ucfirst($prescription->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Submitted:</label>
                                <p>{{ $prescription->created_at->format('M d, Y - h:i A') }}</p>
                            </div>
                            <div class="col-md-4">
                                @if($prescription->reviewed_at)
                                    <label class="form-label fw-bold">Reviewed:</label>
                                    <p>{{ $prescription->reviewed_at->format('M d, Y - h:i A') }}</p>
                                    @if($prescription->reviewer)
                                        <small class="text-muted">By: {{ $prescription->reviewer->name }}</small>
                                    @endif
                                @else
                                    <label class="form-label fw-bold text-warning">Awaiting Review</label>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-3">
                            <hr>
                            <label class="form-label fw-bold">Quick Actions:</label>
                            <div class="btn-group w-100" role="group">
                                @if($prescription->status != 'approved')
                                    <form action="{{ route('admin.prescriptions.update-status', $prescription) }}" method="POST" class="flex-fill">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                    </form>
                                @endif

                                @if($prescription->status != 'rejected')
                                    <button type="button" class="btn btn-danger flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                @endif

                                @if($prescription->status != 'pending')
                                    <form action="{{ route('admin.prescriptions.update-status', $prescription) }}" method="POST" class="flex-fill">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="pending">
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Notes -->
                @if($prescription->notes)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Customer Notes</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $prescription->notes }}</p>
                        </div>
                    </div>
                @endif

                <!-- Admin Notes -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Pharmacy Notes</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.prescriptions.update-notes', $prescription) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <textarea name="admin_notes" class="form-control @error('admin_notes') is-invalid @enderror"
                                          rows="4" placeholder="Add notes visible to the customer...">{{ old('admin_notes', $prescription->admin_notes) }}</textarea>
                                @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-info mt-2">
                                <i class="bi bi-save"></i> Save Notes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Uploaded Files -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-paperclip"></i> Uploaded Files ({{ $prescription->files->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($prescription->files->count() > 0)
                            <div class="row">
                                @foreach($prescription->files as $file)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <!-- File Preview -->
                                                @if(str_contains($file->mime_type, 'image'))
                                                    <div class="text-center mb-2">
                                                        <img src="{{ Storage::url($file->file_path) }}"
                                                             class="img-fluid"
                                                             style="max-height: 200px; cursor: pointer;"
                                                             data-bs-toggle="modal"
                                                             data-bs-target="#imageModal{{ $file->id }}">
                                                    </div>
                                                @else
                                                    <div class="text-center mb-2">
                                                        <i class="bi bi-file-earmark-pdf" style="font-size: 4rem; color: #dc3545;"></i>
                                                    </div>
                                                @endif

                                                <!-- File Info -->
                                                <h6 class="mb-1">{{ $file->file_name }}</h6>
                                                <small class="text-muted d-block mb-2">
                                                    {{ number_format($file->file_size / 1024 / 1024, 2) }} MB •
                                                    {{ $file->created_at->format('M d, Y') }}
                                                </small>

                                                <!-- Actions -->
                                                <div class="btn-group w-100">
                                                    <a href="{{ Storage::url($file->file_path) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('admin.prescriptions.download-file', $file->id) }}"
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Image Modal -->
                                    @if(str_contains($file->mime_type, 'image'))
                                        <div class="modal fade" id="imageModal{{ $file->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ $file->file_name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ Storage::url($file->file_path) }}" class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
                <!-- Customer Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person"></i> Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $prescription->customer->name }}</strong></p>
                        <p class="mb-1">
                            <i class="bi bi-envelope"></i> {{ $prescription->customer->email }}
                        </p>
                        @if($prescription->customer->phone)
                            <p class="mb-0">
                                <i class="bi bi-telephone"></i> {{ $prescription->customer->phone }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item active">
                                <i class="bi bi-upload"></i>
                                <div>
                                    <strong>Submitted</strong>
                                    <small class="d-block text-muted">{{ $prescription->created_at->format('M d, Y - h:i A') }}</small>
                                </div>
                            </div>
                            @if($prescription->reviewed_at)
                                <div class="timeline-item active">
                                    <i class="bi bi-{{ $prescription->status == 'approved' ? 'check-circle' : 'x-circle' }}"></i>
                                    <div>
                                        <strong>{{ ucfirst($prescription->status) }}</strong>
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
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.prescriptions.update-status', $prescription) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Prescription</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Please provide a clear reason for rejecting this prescription. The customer will see this message.
                        </div>
                        <div class="form-group">
                            <label for="admin_notes">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="admin_notes" id="admin_notes"
                                      class="form-control @error('admin_notes') is-invalid @enderror"
                                      rows="4" required
                                      placeholder="e.g., Prescription is not clear, Missing doctor's signature, etc."></textarea>
                            @error('admin_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Reject Prescription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('page_styles')
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