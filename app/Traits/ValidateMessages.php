<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;

trait ValidateMessages
{
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages($data = null): array
    {
        $messages = [
            'required' => 'The :attribute field is required',
            'required_with' => 'The :attribute field is required',
            'exists' => 'The :attribute data not exists',
            'date' => 'The :attribute is must be date',
            'boolean' => 'The :attribute field accept boolean value',
            'image' => 'The :attribute field is must be image',
            'unique' => 'The :attribute field is must be unique',
            'email' => 'The :attribute field accept valid email address',
            'numeric' => 'The :attribute field accept numeric value',
            'integer' => 'The :attribute field accept integer value ',
            'min' => 'The :attribute should not be less than :min',
            'max' => 'The :attribute should not be more than :max',
            'same' => 'New password and confirm password is not matched',
            'different' => 'New password cannot be same as current password',
            'in' => 'The :attribute must be one of the following types: :values',
            'mimes' => 'The :attribute must be one of the following types: :values',
            'array' => 'The :attribute field is must be an array',
            'string' => 'The :attribute field is must be an string',
            'file' => 'The :attribute must be a file.',
            'between' => 'Select :attribute between :min to :max values',
            'required_if' => 'This :attribute field is required.',
            'required_if_accepted' => 'This :attribute field is required.',
            'url' => 'Invalid :attribute provided.',
            'current_password' => ':attribute not matched',
            'date_format' => ':attribute format is invalid!',
            'uploaded' => 'The :attribute failed to upload.',
            'dropout_grade.regex' => 'Dropout Grade/Class is invalid',
            'gt' => 'The :attribute field must be greater than :value.',
            'required_without' => 'The :attribute field is required when :values is not present.',
            'regex' => 'The :attribute is invalid.'
        ];

        return $this->filterByRules($messages);
    }

    private function filterByRules($messages): array
    {
        $results = [];
        foreach ($this->rules() as $column => $rules) {

            if (is_string($rules)) {
                $rules = explode('|', $rules);
            }

            foreach ($rules as $rule) {
                if (!is_string($rule)) {
                    continue;
                }

                $rule = explode(':', $rule)[0];

                if (array_key_exists($rule, $messages)) {
                    $results[$rule] = $messages[$rule];
                }
            }
        }

        return $results;
    }

    /**
     * Sets a flash message for validation failure.
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        session()->flash('warning', 'Please check for error messages and then try again.');

        parent::failedValidation($validator);
    }
}
