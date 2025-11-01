<?php

namespace Modules\Prescription\DataTables;

use Modules\Prescription\Entities\Prescription;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PrescriptionDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('prescription::admin.prescriptions.partials.actions', compact('data'));
            })
            ->addColumn('customer_name', function ($data) {
                return $data->customerUser->name ?? 'N/A';
            })
            ->addColumn('customer_email', function ($data) {
                return $data->customerUser->email ?? 'N/A';
            })
            ->addColumn('files_count', function ($data) {
                return $data->files->count() . ' file(s)';
            })
            ->addColumn('status', function ($data) {
                $badges = [
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                ];
                $class = $badges[$data->status] ?? 'secondary';
                return '<span class="badge bg-'.$class.'">'.ucfirst($data->status).'</span>';
            })
            ->addColumn('submission_date', function ($data) {
                return $data->created_at->format('M d, Y h:i A');
            })
            ->addColumn('reviewed_date', function ($data) {
                return $data->reviewed_at ? $data->reviewed_at->format('M d, Y h:i A') : '-';
            })
            ->rawColumns(['status', 'action']);
    }

    public function query(Prescription $model)
    {
        return $model->newQuery()
            ->with(['customerUser', 'files', 'reviewer'])
            ->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('prescription-table')
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
            Column::make('reference')
                ->title('Reference')
                ->className('text-center align-middle'),

            Column::computed('customer_name')
                ->title('Customer')
                ->className('text-center align-middle'),

            Column::computed('customer_email')
                ->title('Email')
                ->className('text-center align-middle'),

            Column::computed('files_count')
                ->title('Files')
                ->className('text-center align-middle'),

            Column::computed('status')
                ->title('Status')
                ->className('text-center align-middle'),

            Column::computed('submission_date')
                ->title('Submitted')
                ->className('text-center align-middle'),

            Column::computed('reviewed_date')
                ->title('Reviewed')
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
        return 'Prescriptions_' . date('YmdHis');
    }
}