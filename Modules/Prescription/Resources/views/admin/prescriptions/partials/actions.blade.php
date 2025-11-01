<div class="btn-group">
    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-cog"></i> Actions <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li>
            <a class="dropdown-item" href="{{ route('admin.prescriptions.show', $data->id) }}">
                <i class="bi bi-eye"></i> View Details
            </a>
        </li>

        @if($data->status == 'pending')
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('admin.prescriptions.update-status', $data->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="dropdown-item text-success">
                        <i class="bi bi-check-circle"></i> Approve
                    </button>
                </form>
            </li>
            <li>
                <a class="dropdown-item text-danger" href="#"
                   data-bs-toggle="modal"
                   data-bs-target="#rejectModal{{ $data->id }}">
                    <i class="bi bi-x-circle"></i> Reject
                </a>
            </li>
        @endif

        @if($data->status != 'pending')
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('admin.prescriptions.update-status', $data->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="pending">
                    <button type="submit" class="dropdown-item">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset to Pending
                    </button>
                </form>
            </li>
        @endif
    </ul>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal{{ $data->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.prescriptions.update-status', $data->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel{{ $data->id }}">Reject Prescription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="admin_notes{{ $data->id }}">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="admin_notes" id="admin_notes{{ $data->id }}" class="form-control" rows="3" required
                                  placeholder="Please provide a reason for rejecting this prescription..."></textarea>
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