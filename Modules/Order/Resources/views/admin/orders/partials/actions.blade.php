<div class="btn-group">
    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-cog"></i> Actions <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li>
            <a class="dropdown-item" href="{{ route('admin.customer-orders.show', $data->id) }}">
                <i class="bi bi-eye"></i> View Details
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>

        @if($data->status == 'pending')
            <li>
                <form action="{{ route('admin.customer-orders.update-status', $data->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="preparing">
                    <button type="submit" class="dropdown-item">
                        <i class="bi bi-hourglass-split"></i> Mark as Preparing
                    </button>
                </form>
            </li>
        @endif

        @if($data->status == 'preparing')
            <li>
                <form action="{{ route('admin.customer-orders.update-status', $data->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="ready">
                    <button type="submit" class="dropdown-item">
                        <i class="bi bi-check-circle"></i> Mark as Ready
                    </button>
                </form>
            </li>
        @endif

        @if($data->status == 'ready')
            <li>
                <form action="{{ route('admin.customer-orders.update-status', $data->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="dropdown-item">
                        <i class="bi bi-check-all"></i> Mark as Completed
                    </button>
                </form>
            </li>
        @endif

        @if($data->status != 'completed' && $data->status != 'cancelled')
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('admin.customer-orders.update-status', $data->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to cancel this order?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-x-circle"></i> Cancel Order
                    </button>
                </form>
            </li>
        @endif
    </ul>
</div>