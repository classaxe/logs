<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', Rule::exists(User::class, 'id')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->id)],
            'call' => ['required', 'string', 'max:12'],
            'qth' => ['nullable', 'string', 'max:40'],
            'city' => ['required', 'string', 'max:40'],
            'sp' => ['nullable', 'string', 'size:2'],
            'itu' => ['required', 'string', 'regex:/^[A-Z]{2}$/'],
            'gsq' => ['required', 'string', 'regex:/^(?:[a-rA-R]{2}[0-9]{2}[a-xA-X]{2}|[a-rA-R]{2}[0-9]{2}[a-xA-X]{2}[0-9]{2})$/'],
            'qth_names' => ['nullable', 'string'],
            'qrz_api_key' => ['required', 'string', 'min:19, max:19'],
            'clublog_email' => ['nullable', 'string'],
            'clublog_password' => ['nullable', 'string'],
            'clublog_call' => ['nullable', 'string'],
        ];
    }
}
