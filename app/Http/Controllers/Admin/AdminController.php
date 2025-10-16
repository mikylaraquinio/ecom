<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\User;
use App\Models\Report;

class AdminController extends Controller
{
    private function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }
    }

    public function pendingSellers()
    {
        $this->authorizeAdmin();

        $pendingSellers = Seller::where('status', 'pending')->get();
        $reports = Report::with('user')->latest()->get(); // add reports here

        return view('admin.dashboard', compact('pendingSellers', 'reports'));
    }

    public function approveSeller($id)
    {
        $this->authorizeAdmin();

        $seller = Seller::findOrFail($id);
        $seller->update([
            'status' => 'approved',
            'verified_at' => now(),
        ]);

        if ($seller->user) {
            $seller->user->update(['role' => 'seller']);
        }

        return redirect()->back()->with('success', 'Seller approved successfully.');
    }

    public function denySeller($id)
    {
        $this->authorizeAdmin();

        $seller = Seller::findOrFail($id);
        $seller->update(['status' => 'rejected']);

        if ($seller->user) {
            $seller->user->update(['role' => 'buyer']);
        }

        return redirect()->back()->with('error', 'Seller denied.');
    }

}
