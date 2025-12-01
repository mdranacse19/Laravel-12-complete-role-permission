<?php

namespace App\Actions\Association;

use App\Models\Setup\AssociationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class UpdateAssociation
{
    /**
     * Update an existing association.
     *
     * @param  AssociationType  $associationType
     * @param  array  $input
     * @return AssociationType
     * @throws ValidationException
     */
    public function update(AssociationType $associationType, array $input): AssociationType
    {
        $this->validate($associationType, $input);

        $associationType->update([
            'name' => $input['name'],
            'description' => $input['description'] ?? null,
            'app_key' => $input['appKey'] ?? $associationType->app_key,
            'valid_until' => $input['validUntil'] ?? $associationType->valid_until,
            'token' => $input['token'] ?? $associationType->token,
            'is_active' => $input['isActive'] ?? $associationType->is_active,
        ]);

        Log::info('Association updated', $associationType->toArray());

        return $associationType->fresh();
    }

    /**
     * Validate the input data.
     *
     * @param  AssociationType  $associationType
     * @param  array  $input
     * @return void
     * @throws ValidationException
     */
    protected function validate(AssociationType $associationType, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique('association_types')->ignore($associationType->id)],
            'description' => ['nullable', 'string'],
            'appKey' => ['nullable', 'string', Rule::unique('association_types', 'app_key')->ignore($associationType->id)],
            'validUntil' => ['nullable', 'date', 'after:today'],
            'token' => ['nullable', 'string', Rule::unique('association_types')->ignore($associationType->id)],
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
