<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;

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

        return Redirect::route('user.profile')->with('status', 'Profile updated successfully!');
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
    }
