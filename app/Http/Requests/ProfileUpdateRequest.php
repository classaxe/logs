<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'call' => ['required', 'string', 'max:12'],
            'qth' => ['nullable', 'string', 'max:40'],
            'city' => ['required', 'string', 'max:40'],
            'sp' => ['nullable', 'string', 'size:2'],
            'itu' => ['required', 'string', 'size:3'],
            'gsq' => ['required', 'string', 'size:6'],
            'qth_names' => ['nullable', 'string'],
            'qrz_api_key' => ['required', 'string', 'min:19, max:19'],
        ];
    }
}
