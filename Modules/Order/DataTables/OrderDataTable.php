<?php

namespace Modules\Order\DataTables;

use Modules\Order\Entities\Order;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class OrderDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('order::admin.orders.partials.actions', compact('data'));
            })
            ->addColumn('customer_name', function ($data) {
                return $data->customer->name ?? 'N/A';
            })
            ->addColumn('customer_email', function ($data) {
                return $data->customer->email ?? 'N/A';
            })
            ->addColumn('items_count', function ($data) {
                return $data->items->count() . ' item(s)';
            })
            ->addColumn('total', function ($data) {
                return format_currency($data->total);
            })
            ->addColumn('status', function ($data) {
                $badges = [
                    'pending' => 'warning',
                    'preparing' => 'info',
                    'ready' => 'success',
                    'completed' => 'primary',
                    'cancelled' => 'danger',
                ];
                $class = $badges[$data->status] ?? 'secondary';
                return '<span class="badge bg-'.$class.'">'.ucfirst($data->status).'</span>';
            })
            ->addColumn('order_date', function ($data) {
                return $data->created_at->format('M d, Y h:i A');
            })
            ->rawColumns(['status', 'action']);
    }

    public function query(Order $model)
    {
        return $model->newQuery()
            ->with(['customerUser', 'items'])
            ->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('order-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(0, 'desc')
            ->buttons(
                Button::make('excel')
                    ->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')
                    ->text('<i class="bi bi-printer-fill"></i> Print'),
                Button::make('reset')
                    ->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')
                    ->text('<i class="bi bi-arrow-repeat"></i> Reload')
            );
    }

    protected function getColumns()
    {
        return [
            Column::make('id')
                ->title('Order ID')
                ->className('text-center align-middle'),

            Column::computed('customer_name')
                ->title('Customer')
                ->className('text-center align-middle'),

            Column::computed('customer_email')
                ->title('Email')
                ->className('text-center align-middle'),

            Column::computed('items_count')
                ->title('Items')
                ->className('text-center align-middle'),

            Column::computed('total')
                ->title('Total')
                ->className('text-center align-middle'),

            Column::computed('status')
                ->title('Status')
                ->className('text-center align-middle'),

            Column::computed('order_date')
                ->title('Order Date')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Orders_' . date('YmdHis');
    }
}