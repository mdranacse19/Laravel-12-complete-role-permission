<?php

namespace App\Http\Controllers\Dashboard\Form;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Actions\Form\StoreFormBuilderAction;
use App\Actions\Form\UpdateFormBuilderAction;
use App\Enums\FormType;
use App\Http\Requests\Form\FormBuilderRequest;
use App\Models\Form\DynamicForm;
use App\Models\Form\DynamicFormInput;
use App\Models\Setup\FormInput;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FormBuilderController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', DynamicForm::class);

        $forms = DynamicForm::query()
            ->when($request->get('search'), fn ($query, $search) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderByDesc('id')
            ->paginate($request->input('per_page', 15));

        $forms->getCollection()->transform(function ($item) {
            $item->type_name = $item->type->label();
            $item->type_tag = $item->type->tag();

            return $item;
        });

        return Inertia::render('dashboard/setup/dynamic-form/Index', [
            'forms' => $forms,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', DynamicForm::class);

        $types = FormType::options();

        return Inertia::render('dashboard/setup/dynamic-form/Create', [
            'types' => $types,
        ]);
    }

    public function store(FormBuilderRequest $request, StoreFormBuilderAction $action): RedirectResponse
    {
        try {
            $action->handle($request);

            return redirect(route('setup.dynamic-form.index'))->with('success', 'Form created successfully!');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['Form create']);

            return back()->with('error', 'Unable to create the form. Please try again later.');
        }
    }

    public function edit(int $id): Response
    {
        $types = FormType::options();

        $form = DynamicForm::query()
            ->with('inputs')
            ->findOrFail($id);

        $elements = $form->inputs->map(fn ($item) => [
            'input_id' => $item->pivot->form_input_id,
            'type' => $item->type,
            'label' => $item->pivot->label,
            'placeholder' => $item->pivot->placeholder,
            'options' => $item->pivot->options,
            'required' => $item->pivot->required,
            'has_action' => $item->pivot->has_action,
        ])->toArray();

        $editData = [
            'form_id' => $form->id,
            'name' => $form->name,
            'status' => $form->status,
            'elements' => $elements,
        ];

        return Inertia::render(
            'dashboard/setup/dynamic-form/Create', [
                'form' => $editData,
                'types' => $types,
            ]
        );
    }

    public function update(int $id, FormBuilderRequest $request, UpdateFormBuilderAction $action): RedirectResponse
    {
        try {
            $action->handle($id, $request);

            return redirect(route('setup.dynamic-form.index'))
                ->with('success', 'Form updated successfully!');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [
                'context' => 'Form update',
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Unable to update the form. Please try again later.');
        }
    }

    public function inputs(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Form inputs retrieved successfully.',
            'data' => FormInput::query()->select('*', 'name as label')->get(),
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        DynamicForm::query()->findOrFail($id)->delete();

        return response()->json([
            'message' => 'Form has been permanently removed.',
        ]);

    }
}
