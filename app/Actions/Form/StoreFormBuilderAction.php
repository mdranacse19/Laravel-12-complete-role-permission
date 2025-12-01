<?php

namespace App\Actions\Form;

use App\Http\Requests\Form\FormBuilderRequest;
use App\Models\Form\DynamicForm;
use App\Models\Form\DynamicFormInput;
use Illuminate\Support\Facades\DB;

class StoreFormBuilderAction
{
    /**
     * Handle creating a new DynamicForm with inputs
     */
    public function handle(FormBuilderRequest $request): DynamicForm
    {
        return DB::transaction(function () use ($request) {
            $id = $request->get('form_id');

            $formData = $request->only('name', 'type');
            $formData['is_active'] = $request->input('status') === "1";

            $form = DynamicForm::query()->create($formData);

            $elements = collect($request->input('elements', []))->map(fn ($item, $sort) => [
                'dynamic_form_id' => $form->id,
                'form_input_id' => $item['input_id'],
                'label' => $item['label'] ?? null,
                'placeholder' => $item['placeholder'] ?? null,
                'options' => $item['options'] ?? null,
                'required' => (bool)($item['required'] ?? false),
                'has_action' => (bool)($item['has_action'] ?? false),
                'sort' => $sort,
            ]);

            if ($elements->isNotEmpty()) {
                DynamicFormInput::insert($elements->toArray());
            }

            return $form;
        });
    }
}
