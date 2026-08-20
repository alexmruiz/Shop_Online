<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = Auth::user();
        return [
            'name' => 'required|min:3|max:255',
            'email' => "required|email|max:255|unique:users,email,{$user->id}",
            'password' => 'required|min:6',
            'confirmpassword' => 'required|same:password',
            'role' => 'required|string|max:255',
        ];
    }
}
