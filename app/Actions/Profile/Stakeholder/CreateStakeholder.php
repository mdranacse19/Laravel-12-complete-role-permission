<?php

namespace App\Actions\Profile\Stakeholder;

use App\Enums\StakeholderType;
use App\Models\Profile\Stakeholder;
use App\Rules\BDMobileNumber;
use App\Rules\ValidEmailRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateStakeholder
{
    /**
     * Create a new stakeholder.
     *
     * @param  array  $input
     * @return Stakeholder
     * @throws ValidationException
     */
    public function create(array $input): Stakeholder
    {
        $this->validate($input);

        return Stakeholder::create([
            'type' => $input['type'],
            'name' => $input['name'],
            'bn_name' => $input['bnName'] ?? null,
            'designation' => $input['designation'] ?? null,
            'bn_designation' => $input['bnDesignation'] ?? null,
            'mobile' => $input['mobileNo'] ?? null,
            'email' => $input['email'] ?? null,
            'password' => $input['password'] ?? null,
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
            'type' => ['required', 'string', Rule::enum(StakeholderType::class)],
            'name' => ['required', 'string', 'max:255'],
            'bnName' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string'],
            'bnDesignation' => ['nullable', 'string'],
            'mobileNo' => ['required', 'string', Rule::unique(Stakeholder::class, 'mobile'), new BDMobileNumber],
            'email' => ['required', 'email', 'max:250', new ValidEmailRule, Rule::unique(Stakeholder::class, 'email')],
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
