@extends('customer::layouts.app')

@section('title', 'My Prescriptions')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-file-earmark-medical"></i> My Prescriptions</h2>
            <a href="{{ route('customer.prescriptions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Upload New Prescription
            </a>
        </div>

        @if($prescriptions->count() > 0)
            <div class="row">
                @foreach($prescriptions as $prescription)
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <strong>Ref: {{ $prescription->reference }}</strong><br>
                                        <small class="text-muted">{{ $prescription->created_at->format('M d, Y - h:i A') }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="badge 
                                            @if($prescription->status == 'pending') bg-warning
                                            @elseif($prescription->status == 'approved') bg-success
                                            @elseif($prescription->status == 'rejected') bg-danger
                                            @elseif($prescription->status == 'processing') bg-info
                                            @endif">
                                            {{ ucfirst($prescription->status) }}
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">
                                            <i class="bi bi-paperclip"></i> {{ $prescription->files->count() }} file(s)
                                        </small>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <a href="{{ route('customer.prescriptions.show', $prescription) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @if($prescription->notes || $prescription->admin_notes)
                                <div class="card-body">
                                    @if($prescription->notes)
                                        <div class="mb-2">
                                            <strong>Your Notes:</strong>
                                            <p class="text-muted mb-0">{{ Str::limit($prescription->notes, 150) }}</p>
                                        </div>
                                    @endif
                                    @if($prescription->admin_notes)
                                        <div class="alert alert-info mb-0 mt-2">
                                            <strong><i class="bi bi-info-circle"></i> Pharmacy Notes:</strong>
                                            <p class="mb-0">{{ $prescription->admin_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $prescriptions->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-earmark-medical-fill" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3">No prescriptions uploaded</h4>
                    <p class="text-muted">Upload your prescription to get started!</p>
                    <a href="{{ route('customer.prescriptions.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle"></i> Upload Prescription
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection