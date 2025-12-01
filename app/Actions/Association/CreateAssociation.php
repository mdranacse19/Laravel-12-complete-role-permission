<?php

namespace App\Actions\Association;

use App\Models\Setup\AssociationType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateAssociation
{
    /**
     * Create a new association.
     *
     * @param  array  $input
     * @return Association
     * @throws ValidationException
     */
    public function create(array $input): AssociationType
    {
        $this->validate($input);

        return AssociationType::create([
            'name' => $input['name'],
            'description' => $input['description'] ?? null,
            'app_key' => $input['appKey'] ?? null,
            'valid_until' => $input['validUntil'] ?? null,
            'token' => $input['token'] ?? null,
            'is_active' => $input['isActive'] ?? true,
        ]);
    }

    /**
     * Validate the input data.
     *
     * @param  array  $input
     * @return void
     * @throws ValidationException
     */
    protected function validate(array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'unique:association_types,name'],
            'description' => ['nullable', 'string'],
            'appKey' => ['nullable', 'string', 'unique:association_types,app_key'],
            'validUntil' => ['nullable', 'date', 'after:today'],
            'token' => ['nullable', 'string', 'unique:association_types,token'],
            'isActive' => ['nullable', 'boolean'],
        ])->validate();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'appKey' => 'app key',
            'validUntil' => 'valid until',
            'isActive' => 'is active',
        ];
    }
}
