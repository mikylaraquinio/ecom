<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'birthdate' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user()->id()],
        ];
    }
}
