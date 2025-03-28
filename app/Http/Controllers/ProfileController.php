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
            'phone' => 'required|string|max:15',
            'birthdate' => 'required|date',
            'name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);


        if (!$user instanceof User) {
            abort(500, 'User instance not found.');
        }


        $user->update([
            'username' => $request->username,
            'phone' => $request->phone,
            'birthdate' => $request->birthdate,
            'name' => $request->name,
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

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Retrieve user properly
        $user = Auth::user();

        if (!$user) {
            return back()->withErrors(['error' => 'User not authenticated.']);
        }

        // Debugging: Ensure $user is an instance of User
        if (!$user instanceof User) {
            return back()->withErrors(['error' => 'User model not found.']);
        }

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if not the default
            if ($user->profile_picture && $user->profile_picture !== 'assets/default.png') {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store the new image
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;

            // Debugging: Confirm before saving
            if (method_exists($user, 'save')) {
                $user->save();
            } else {
                return back()->withErrors(['error' => 'Save method does not exist on User model.']);
            }
        }

        return back()->with('success', 'Profile picture updated successfully.');
    }
    // In your controller
    public function showProfile()
    {
        $user = auth()->user();

        // Fetch orders with order items and products
        $ordersToShip = $user->orders()
            ->whereIn('status', ['pending', 'accepted']) // Fetch both statuses
            ->with('orderItems.product')
            ->get();

        $ordersToReceive = $user->orders()
            ->where('status', 'shipped') // Orders accepted by the seller
            ->with('orderItems.product')
            ->get();

        $ordersToReview = $user->orders()
            ->where('status', 'completed') // Completed orders ready for review
            ->with('orderItems.product')
            ->get();

        return view('user_profile', compact('ordersToShip', 'ordersToReceive', 'ordersToReview'));
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

}