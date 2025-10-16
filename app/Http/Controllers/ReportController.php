<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Store a new report submitted by a user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'severity' => 'nullable|string|max:50',
            'description' => 'required|string',
            'contact_email' => 'nullable|email|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'target_id' => 'nullable|integer',
            'target_type' => 'nullable|string|max:100',
        ]);

        // Save uploaded file if present
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('reports', 'public');
        }

        Report::create([
            'user_id' => Auth::id(),
            'target_id' => $request->target_id,
            'target_type' => $request->target_type,
            'category' => $request->category,
            'severity' => $request->severity,
            'description' => $request->description,
            'contact_email' => $request->contact_email,
            'attachment' => $attachmentPath,
            'status' => 'Pending',
        ]);

        return back()->with('success', '✅ Your report has been submitted successfully!');
    }

    /**
     * Admin view - show all reports.
     */
    public function index()
    {
        $reports = Report::with('user')->latest()->get();
        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Admin updates report status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Reviewed,Resolved',
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        return back()->with('success', 'Report status updated.');
    }

}
