<?php

namespace Modules\Prescription\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Prescription\Entities\Prescription;
use Modules\Prescription\Entities\PrescriptionFile;

class PrescriptionController extends Controller
{
    public function index()
    {
        $prescriptions = Prescription::where('customer_user_id', auth('customer')->id())
            ->with('files')
            ->latest()
            ->paginate(10);

        return view('prescription::prescriptions.index', compact('prescriptions'));
    }

    public function create()
    {
        return view('prescription::prescriptions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB max
        ]);

        DB::beginTransaction();
        try {
            $prescription = Prescription::create([
                'customer_user_id' => auth('customer')->id(),
                'reference' => Prescription::generateReference(),
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('prescriptions', $fileName, 'public');

                    PrescriptionFile::create([
                        'prescription_id' => $prescription->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('customer.prescriptions.index')
                ->with('success', 'Prescription uploaded successfully! Reference: ' . $prescription->reference);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload prescription: ' . $e->getMessage());
        }
    }

    public function show(Prescription $prescription)
    {
        // Ensure customer can only view their own prescriptions
        if ($prescription->customer_user_id !== auth('customer')->id()) {
            abort(403);
        }

        $prescription->load('files', 'reviewer');

        return view('prescription::prescriptions.show', compact('prescription'));
    }
}