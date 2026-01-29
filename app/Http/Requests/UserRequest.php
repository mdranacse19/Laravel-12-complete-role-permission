<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\BDMobileNumber;
use App\Rules\UniqueEmailAcrossProfiles;
use App\Rules\ValidEmailRule;
use App\Rules\ValidNameRule;
use App\Traits\ConvertsDataTypes;
use App\Traits\ValidateMessages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserRequest extends FormRequest
{
    use ConvertsDataTypes, ValidateMessages;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'fullName' => ['required', 'string', 'max:64', new ValidNameRule],
            'bengaliName' => ['nullable', 'string', 'max:64'],
            'designation' => ['nullable', 'string', 'max:255'],
            'bnDesignation' => ['nullable', 'string', 'max:255'],
            'emailAddress' => [
                'nullable',
                'string',
                'email',
                'max:255',
                "unique:users,email,{$this->route('user')?->id}",
                new UniqueEmailAcrossProfiles
            ],
            'role' => ['required', 'numeric', Rule::exists(Role::class, 'id')],
          
            'isActive' => ['bool'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'fullName' => 'full name',
            'bengaliName' => 'bengali name',
            'emailAddress' => 'email address',
            'designation' => 'designation',
            'bnDesignation' => 'bengali designation',
            'role' => 'user role',
            'isActive' => 'is active',
        ];
    }

    /**
     * Returns data for new user.
     */
    public function getFormData(): array
    {
        return [
            'name' => $this->convertString('fullName'),
            'bn_name' => $this->convertString('bengaliName'),
            'email' => Str::lower($this->convertString('emailAddress')),
            'designation' => $this->convertString('designation'),
            'bn_designation' => $this->convertString('bnDesignation'),
            'password' => Hash::make($this->get('password')),
            'is_active' => $this->boolean('isActive'),
        ];
    }
}
