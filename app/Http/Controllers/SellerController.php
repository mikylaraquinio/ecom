<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Seller;

class SellerController extends Controller
{
    public function sell()
    {
        return view('farmers.modal.sell');  // or 'farmers.modal.sell' if that's the path you want
    }

    public function storeSeller(Request $request)
{
    $request->validate([
        'farm_name' => 'required|string|max:255',
        'farm_address' => 'required|string|max:255',
        'gov_id' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        'farm_certificate' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        'mobile_money' => 'nullable|string|max:20',
        'terms' => 'required|accepted',
    ]);

    // Get authenticated user
    $user = Auth::user();

    if (!$user || !$user instanceof User) {
        return redirect()->back()->with('error', 'User not found or not authenticated.');
    }

    // Handle file uploads
    $govIdPath = $request->hasFile('gov_id') ? $request->file('gov_id')->store('documents', 'public') : $user->gov_id;
    $farmCertPath = $request->hasFile('farm_certificate') ? $request->file('farm_certificate')->store('documents', 'public') : $user->farm_certificate;

    // Manually updating attributes instead of using update()
    $user->farm_name = $request->farm_name;
    $user->farm_address = $request->farm_address;
    $user->gov_id = $govIdPath;
    $user->farm_certificate = $farmCertPath;
    $user->mobile_money = $request->mobile_money;
    $user->role = 'seller'; // Change user role to seller

    $user->save(); // Save changes to the database

    return redirect()->route('user_profile')->with('success', 'Seller registration successful!');
}

}
