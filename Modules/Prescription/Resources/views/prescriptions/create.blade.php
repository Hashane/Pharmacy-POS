@extends('customer::layouts.app')

@section('title', 'Upload Prescription')

@section('content')
    <div class="py-4">
        <h2 class="mb-4"><i class="bi bi-file-earmark-medical"></i> Upload Prescription</h2>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Submit Your Prescription</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customer.prescriptions.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Notes -->
                            <div class="form-group mb-3">
                                <label for="notes">Notes / Instructions <span class="text-muted">(Optional)</span></label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                          rows="3" placeholder="Any additional information or special instructions...">{{ old('notes') }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Please provide any relevant details about your prescription.</small>
                            </div>

                            <!-- File Upload -->
                            <div class="form-group mb-3">
                                <label for="prescription_files">Upload Prescription Files <span class="text-danger">*</span></label>
                                <input type="file" name="prescription_files[]" id="prescription_files"
                                       class="form-control @error('prescription_files') is-invalid @enderror @error('prescription_files.*') is-invalid @enderror"
                                       multiple accept="image/*,.pdf" required>
                                @error('prescription_files')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('prescription_files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    You can upload multiple files. Accepted formats: JPG, PNG, PDF (Max 5MB per file)
                                </small>
                            </div>

                            <!-- File Preview -->
                            <div id="filePreview" class="mb-3" style="display: none;">
                                <label>Selected Files:</label>
                                <div id="fileList" class="list-group"></div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-upload"></i> Submit Prescription
                                </button>
                                <a href="{{ route('customer.prescriptions.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> View My Prescriptions
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Sidebar -->
            <div class="col-lg-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-info-circle"></i> Important Information</h5>
                        <ul class="mb-0">
                            <li class="mb-2">Upload clear, readable images or PDFs of your prescription</li>
                            <li class="mb-2">Make sure all details are visible</li>
                            <li class="mb-2">You can upload multiple files if needed</li>
                            <li class="mb-2">Our pharmacy staff will review your prescription</li>
                            <li class="mb-0">You'll be notified once it's reviewed</li>
                        </ul>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-shield-check"></i> Privacy & Security</h5>
                        <p class="small mb-0">
                            Your prescription information is securely stored and only accessible to authorized pharmacy staff.
                            We comply with all privacy regulations to protect your health information.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('prescription_files').addEventListener('change', function(e) {
                const fileList = document.getElementById('fileList');
                const filePreview = document.getElementById('filePreview');
                const files = e.target.files;

                fileList.innerHTML = '';

                if (files.length > 0) {
                    filePreview.style.display = 'block';

                    Array.from(files).forEach((file, index) => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'list-group-item d-flex justify-content-between align-items-center';

                        const fileInfo = document.createElement('div');
                        fileInfo.innerHTML = `
                        <i class="bi bi-file-earmark-${file.type.includes('pdf') ? 'pdf' : 'image'}"></i>
                        <strong>${file.name}</strong>
                        <small class="text-muted d-block">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                    `;

                        const fileBadge = document.createElement('span');
                        fileBadge.className = 'badge bg-primary rounded-pill';
                        fileBadge.textContent = file.type.includes('pdf') ? 'PDF' : 'Image';

                        fileItem.appendChild(fileInfo);
                        fileItem.appendChild(fileBadge);
                        fileList.appendChild(fileItem);
                    });
                } else {
                    filePreview.style.display = 'none';
                }
            });
        </script>
    @endpush
@endsection