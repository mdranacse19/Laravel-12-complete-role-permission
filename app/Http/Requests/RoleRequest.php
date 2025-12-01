<?php

namespace App\Http\Requests;

use App\Traits\ConvertsDataTypes;
use App\Traits\ValidateMessages;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PermissionsExists;
use Illuminate\Validation\Rule;
use App\Models\Role;

class RoleRequest extends FormRequest
{
    use ConvertsDataTypes, ValidateMessages;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if it's a create or update request
        $permission = $this->isMethod('POST') ? 'role_create' : 'role_update';

        return $this->user() && $this->user()->can($permission);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'roleName' => ['required', 'string', 'max:48'],
            'forPartner' => ['nullable', 'boolean'],
            'permissions' => ['required', 'array', new PermissionsExists],
        ];

        // Add unique rule, ignore current role if updating and ignore soft deleted roles
        if ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            $rules['roleName'][] = Rule::unique(Role::class, 'name')
                ->ignore($this->role->id)
                ->whereNull('deleted_at');
        } else {
            $rules['roleName'][] = Rule::unique(Role::class, 'name')
                ->whereNull('deleted_at');
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'roleName' => 'role name',
            'forPartner' => 'partner role',
        ];
    }
}
