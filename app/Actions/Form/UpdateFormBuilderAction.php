<?php

namespace App\Actions\Form;

use App\Http\Requests\Form\FormBuilderRequest;
use App\Models\Form\DynamicForm;
use App\Models\Form\DynamicFormInput;
use Illuminate\Support\Facades\DB;

class UpdateFormBuilderAction
{
    /**
     * Handle updating an existing DynamicForm with inputs
     */
    public function handle(int $formId, FormBuilderRequest $request): DynamicForm
    {
        return DB::transaction(function () use ($formId, $request) {
            $form = DynamicForm::query()->findOrFail($formId);
            $data = $request->only('name');
            $data['is_active'] = $request->input('status') === "1";
            $form->update($data);

            // Upsert inputs by (dynamic_form_id, form_input_id) and prune extras
            $payload = collect($request->input('elements', []));

            // // Index existing rows by form_input_id for quick lookup
            $existing = DynamicFormInput::query()
                ->where('dynamic_form_id', $form->id)
                ->get();

            $seenInputIds = [];

            $payload->each(function ($item, $sort) use ($form, $existing, &$seenInputIds) {
                $formInputId = $item['input_id'];
                $seenInputIds[] = $formInputId;

                if ($existing->contains(fn($row) => $row->form_input_id == $formInputId)) {
                    // Update existing record
                    $row = $existing->first(fn($row) => $row->form_input_id == $formInputId);
                    $row->label = $item['label'] ?? null;
                    $row->placeholder = $item['placeholder'] ?? null;
                    $row->options = $item['options'] ?? null;
                    $row->required = (bool)($item['required'] ?? false);
                    $row->has_action = (bool)($item['has_action'] ?? false);
                    $row->sort = $sort;
                    $row->save();
                } else {
                    // Create new record
                    DynamicFormInput::query()->create([
                        'dynamic_form_id' => $form->id,
                        'form_input_id' => $formInputId,
                        'label' => $item['label'] ?? null,
                        'placeholder' => $item['placeholder'] ?? null,
                        'options' => $item['options'] ?? null,
                        'required' => (bool)($item['required'] ?? false),
                        'has_action' => (bool)($item['has_action'] ?? false),
                        'sort' => $sort,
                    ]);
                }
            });

            // Delete any DB rows not present in payload
            DynamicFormInput::query()
                ->where('dynamic_form_id', $form->id)
                ->whereNotIn('form_input_id', $seenInputIds)
                ->delete();

            return $form;
        });
    }
}
