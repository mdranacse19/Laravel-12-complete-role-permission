<?php

namespace App\Http\Requests\Form;

use App\Enums\FormType;
use App\Traits\ValidateMessages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormBuilderRequest extends FormRequest
{
    use ValidateMessages;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->can('form_builder_create') || auth()->user()->can('form_builder_update'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::enum(FormType::class)],
            'name' => ['required', 'string', 'max:255'],
            'elements.*.label' => ['required', 'string', 'max:255'],
            'elements.*.type' => ['required', 'string', 'max:255'],
            'elements.*.placeholder' => ['nullable', 'string', 'max:255'],
            'elements.*.options' => ['nullable', 'string'],
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
            'elements.*.label' => 'Label',
            'elements.*.type' => 'Type',
            'elements.*.placeholder' => 'Placeholder',
            'elements.*.options' => 'Options',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('elements', []) as $index => $item) {
                if (in_array($item['type'], ['radio', 'select', 'multiSelect', 'checkbox']) && empty($item['options'])) {
                    $validator->errors()->add("elements.$index.options", "The options field is required when type is {$item['type']}.");
                }
            }
        });
    }
}
