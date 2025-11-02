<?php

namespace Modules\Prescription\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Prescription\DataTables\PrescriptionDataTable;
use Modules\Prescription\Entities\Prescription;

class AdminPrescriptionController extends Controller
{
    /**
     * Display a listing of prescriptions
     */
    public function index(PrescriptionDataTable $dataTable, Request $request)
    {
        // Filter by status if provided
        if ($request->filled('status')) {
            $dataTable->query(function ($query) use ($request) {
                return $query->where('status', $request->status);
            });
        }

        return $dataTable->render('prescription::admin.prescriptions.index');
    }

    /**
     * Display the specified prescription
     */
    public function show(Prescription $prescription)
    {
        $prescription->load(['customer', 'files', 'reviewer']);

        return view('prescription::admin.prescriptions.show', compact('prescription'));
    }

    /**
     * Update prescription status
     */
    public function updateStatus(Request $request, Prescription $prescription)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'required_if:status,rejected|nullable|string|max:1000'
        ]);

        $prescription->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
        ]);

        // You can add notification logic here
        // event(new PrescriptionStatusUpdated($prescription));

        $statusMessage = ucfirst($request->status);
        toast("Prescription {$statusMessage} successfully!", 'success');

        return redirect()->back();
    }

    /**
     * Add or update admin notes
     */
    public function updateNotes(Request $request, Prescription $prescription)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        $prescription->update([
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
        ]);

        toast('Notes updated successfully!', 'success');

        return redirect()->back();
    }

    /**
     * Download prescription file
     */
    public function downloadFile($fileId)
    {
        $file = \Modules\Prescription\Entities\PrescriptionFile::findOrFail($fileId);

        return response()->download(storage_path('app/public/' . $file->file_path), $file->file_name);
    }

    /**
     * Export prescriptions
     */
    public function export()
    {

        return back()->with('info', 'Export functionality to be implemented');
    }
}