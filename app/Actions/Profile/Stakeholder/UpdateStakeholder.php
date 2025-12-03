<?php

namespace App\Actions\Profile\Stakeholder;

use App\Enums\StakeholderType;
use App\Models\Profile\Stakeholder;
use App\Rules\BDMobileNumber;
use App\Rules\ValidEmailRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateStakeholder
{
    /**
     * Update an existing stakeholder.
     *
     * @param  Stakeholder  $stakeholder
     * @param  array  $input
     * @return Stakeholder
     * @throws ValidationException
     */
    public function update(Stakeholder $stakeholder, array $input): Stakeholder
    {
        $this->validate($stakeholder, $input);

        $data = [
            'type' => $input['type'],
            'name' => $input['name'],
            'bn_name' => $input['bnName'] ?? null,
            'designation' => $input['designation'] ?? null,
            'bn_designation' => $input['bnDesignation'] ?? null,
            'mobile' => $input['mobileNo'] ?? null,
            'email' => $input['email'] ?? null,
            'is_active' => $input['isActive'] ?? true,
        ];

        if(!is_null($input['password'])) {
            $data['password'] = $input['password'];
        }

        $stakeholder->update($data);

        Log::info('Association updated', $stakeholder->toArray());

        return $stakeholder->fresh();
    }

    /**
     * Validate the input data.
     *
     * @param  Stakeholder  $stakeholder
     * @param  array  $input
     * @return void
     * @throws ValidationException
     */
    protected function validate(Stakeholder $stakeholder, array $input): void
    {
        Validator::make($input, [
            'type' => ['required', 'string', Rule::enum(StakeholderType::class)],
            'name' => ['required', 'string', 'max:255'],
            'bnName' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string'],
            'bnDesignation' => ['nullable', 'string'],
            'mobileNo' => ['required', 'string', Rule::unique(Stakeholder::class, 'mobile')->ignore($stakeholder->id), new BDMobileNumber],
            'email' => ['required', 'email', 'max:250', new ValidEmailRule, Rule::unique(Stakeholder::class, 'email')->ignore($stakeholder->id)],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
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
            'bnName' => 'bangla name',
            'bnDesignation' => 'bangla designation',
            'mobileNo' => 'mobile number',
            'isActive' => 'is active',
        ];
    }
}
