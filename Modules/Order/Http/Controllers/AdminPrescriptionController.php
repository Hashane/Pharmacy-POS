<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Prescription\Entities\Prescription;

class AdminPrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with(['customerUser', 'files', 'reviewer']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $prescriptions = $query->latest()->paginate(15);

        return view('admin.prescriptions::index', compact('prescriptions'));
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['customerUser', 'files', 'reviewer']);

        return view('admin.prescriptions::show', compact('prescription'));
    }

    public function approve(Request $request, Prescription $prescription)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $prescription->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Prescription approved successfully!');
    }

    public function reject(Request $request, Prescription $prescription)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $prescription->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Prescription rejected!');
    }
}