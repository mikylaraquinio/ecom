<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('user_profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
{
    if (!Auth::check()) {
        abort(403, 'Unauthorized action. You must be logged in.');
    }

    $user = Auth::user();

    $request->validate([
        'username' => 'required|string|max:255',
        'phone' => 'nullable|string|max:15',
        'birthdate' => 'nullable|date',
        'gender' => 'nullable|string|in:male,female',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
    ]);

    $user->update([
        'username' => $request->username,
        'phone' => $request->phone,
        'birthdate' => $request->birthdate,
        'gender' => $request->gender,
        'email' => $request->email,
    ]);

    return Redirect::route('user_profile')->with('status', 'Profile updated successfully!');
}




    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function sell()
    {
        return view('farmers.sell');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user(); // Ensure this returns an Eloquent model instance

        if (!$user || !($user instanceof User)) {
            return back()->withErrors(['error' => 'User not found.']);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save(); // Ensure save() is being called on a valid Eloquent model

        return back()->with('success', 'Password updated successfully!');
    }

    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => ['required','image','max:2048'], // ~2MB, adjust as needed
        ]);

        $user = $request->user();

        // store to public disk -> storage/app/public/profile_pictures/...
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');

        // optionally delete old file if you stored path previously
        if (!empty($user->profile_picture) && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->profile_picture = $path; // store relative path like "profile_pictures/abc.jpg"
        $user->save();

        return response()->json([
            'success' => true,
            'path' => asset('storage/'.$path), // for instant preview if needed
        ]);
    }
    // In your controller
    public function showProfile()
    {
        $user = auth()->user();

        $ordersToShip = $user->orders()
            ->whereIn('status', ['pending', 'accepted'])
            ->with('orderItems.product.seller', 'address') // include seller
            ->get();

        $ordersToReceive = $user->orders()
            ->where('status', 'shipped')
            ->with('orderItems.product.seller', 'address') // include seller
            ->get();

        $ordersToReview = $user->orders()
            ->where('status', 'completed')
            ->with('orderItems.product.seller', 'address') // include seller
            ->get();

        $wishlistItems = $user->wishlist()
            ->with('seller') // in case wishlist shows seller too
            ->get();

        return view('user_profile', compact(
            'ordersToShip',
            'ordersToReceive',
            'ordersToReview',
            'wishlistItems'
        ));
    }


    public function cancelOrder($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status == 'pending') {
            // Directly cancel if still pending
            $order->update(['status' => 'canceled']);
            return redirect()->back()->with('success', 'Order has been canceled.');
        } elseif ($order->status == 'accepted') {
            // If accepted, request seller approval
            $order->update(['status' => 'cancel_requested']);

            // Notify the seller (if you have a notification system)
            // Notification::send($order->seller, new OrderCancelRequest($order));

            return redirect()->back()->with('success', 'Cancelation request sent to the seller.');
        }

        return redirect()->back()->with('error', 'Order cannot be canceled at this stage.');
    }

    public function confirmReceipt($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('orderItems.product') // ✅ load order items + products
            ->firstOrFail();

        // Reduce stock when completing
        foreach ($order->orderItems as $item) {
            $product = $item->product;

            if ($product) {
                if ($product->stock >= $item->quantity) {
                    $product->stock -= $item->quantity;
                    $product->save();
                } else {
                    return redirect()->back()->with('error', "Not enough stock for {$product->name}.");
                }
            }
        }

        $order->status = 'completed';
        $order->delivered_at = now();
        $order->save();

        return redirect()->route('user_profile')->with('success', 'Order marked as completed.');
    }



}