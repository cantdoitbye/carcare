@extends('admin.layouts.app')

@section('title', 'Coupons')
@section('page-title', 'Coupon Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Coupons</h1>
    <div class="btn-group">
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Coupon
        </a>
        <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" 
                data-bs-toggle="dropdown">
            <span class="visually-hidden">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.coupons.export', ['status' => $status]) }}">
                <i class="fas fa-download me-2"></i>Export Coupons</a></li>
        </ul>
    </div>
</div>

<!-- Status Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="{{ route('admin.coupons.index') }}" 
               class="btn btn-{{ $status === 'all' ? 'primary' : 'outline-primary' }}">
                All Coupons
            </a>
            <a href="{{ route('admin.coupons.index', ['status' => 'active']) }}" 
               class="btn btn-{{ $status === 'active' ? 'success' : 'outline-success' }}">
                Active
            </a>
            <a href="{{ route('admin.coupons.index', ['status' => 'expired']) }}" 
               class="btn btn-{{ $status === 'expired' ? 'danger' : 'outline-danger' }}">
                Expired
            </a>
            <a href="{{ route('admin.coupons.index', ['status' => 'expiring']) }}" 
               class="btn btn-{{ $status === 'expiring' ? 'warning' : 'outline-warning' }}">
                Expiring Soon
            </a>
        </div>
    </div>
</div>

<!-- Bulk Actions -->
<div class="card mb-4" id="bulkActionsCard" style="display: none;">
    <div class="card-body">
        <form id="bulkActionForm" method="POST" action="{{ route('admin.coupons.bulk-action') }}">
            @csrf
            <div class="row align-items-center">
                <div class="col-auto">
                    <span id="selectedCount">0</span> coupons selected
                </div>
                <div class="col-auto">
                    <select class="form-select" name="action" required>
                        <option value="">Choose Action</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="expire">Mark as Expired</option>
                        <option value="delete">Delete</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <button type="button" class="btn btn-secondary" onclick="clearSelection()">Clear</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Coupons Table -->
<div class="card">
    <div class="card-body">
        @if($coupons->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Usage</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input coupon-checkbox" 
                                           value="{{ $coupon->id }}">
                                </td>
                                <td>
                                    <div>
                                        <code class="fw-bold">{{ $coupon->code }}</code>
                                        @if(!$coupon->is_public)
                                            <br>
                                            <small class="text-{{ $coupon->expires_at->isPast() ? 'danger' : ($coupon->expires_at->diffInDays() <= 7 ? 'warning' : 'muted') }}">
                                                {{ $coupon->expires_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    @php $badgeStatus = $coupon->status_badge; @endphp
                                    <span class="badge bg-{{ $badgeStatus === 'active' ? 'success' : ($badgeStatus === 'expired' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($badgeStatus) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.coupons.show', $coupon) }}" 
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    data-bs-toggle="dropdown" title="More">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="{{ route('admin.coupons.duplicate', $coupon) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-copy me-2"></i>Duplicate
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger" 
                                                            onclick="deleteCoupon({{ $coupon->id }})">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                <h5>No coupons found</h5>
                <p class="text-muted">Start by creating your first coupon</p>
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Coupon
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this coupon? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.coupon-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkActions();
});

// Individual checkbox handling
document.querySelectorAll('.coupon-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const selectedCheckboxes = document.querySelectorAll('.coupon-checkbox:checked');
    const bulkActionsCard = document.getElementById('bulkActionsCard');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectedCheckboxes.length > 0) {
        bulkActionsCard.style.display = 'block';
        selectedCount.textContent = selectedCheckboxes.length;
        
        // Update hidden inputs for bulk action
        const bulkForm = document.getElementById('bulkActionForm');
        const existingInputs = bulkForm.querySelectorAll('input[name="selected_ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        selectedCheckboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_ids[]';
            input.value = checkbox.value;
            bulkForm.appendChild(input);
        });
    } else {
        bulkActionsCard.style.display = 'none';
    }
}

function clearSelection() {
    document.querySelectorAll('.coupon-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    updateBulkActions();
}

function deleteCoupon(id) {
    document.getElementById('deleteForm').action = '/admin/coupons/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Bulk action form submission
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const action = this.querySelector('select[name="action"]').value;
    if (action === 'delete') {
        if (!confirm('Are you sure you want to delete the selected coupons? This action cannot be undone.')) {
            e.preventDefault();
        }
    }
});
</script>
@endpush