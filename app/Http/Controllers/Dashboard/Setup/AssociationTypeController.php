<?php

namespace App\Http\Controllers\Dashboard\Setup;

use App\Actions\Setup\AssociationType\CreateAssociation;
use App\Actions\Setup\AssociationType\DeleteAssociation;
use App\Actions\Setup\AssociationType\UpdateAssociation;
use App\Http\Controllers\Controller;
use App\Models\Setup\AssociationType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Resources\Dashboard\Setup\AssociationTypeResource;

class AssociationTypeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of associations.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', AssociationType::class);

        $query = AssociationType::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Sorting
        $orderBy = $request->input('order_by', 'created_at');
        $direction = $request->input('order_direction', 'desc');

        // Only apply ordering if orderBy is not empty
        if (!empty($orderBy)) {
            $query->orderBy($orderBy, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $associationTypes = $query->paginate($perPage);

        return Inertia::render('dashboard/setup/association-types/Index', [
            'associationTypes' => AssociationTypeResource::collection($associationTypes),
            'filters' => $request->only(['search', 'order_by', 'order_direction']),
        ]);
    }

    /**
     * Show the form for creating a new association.
     */
    public function create(): Response
    {
        $this->authorize('create', AssociationType::class);

        return Inertia::render('dashboard/setup/association-types/Create');
    }

    /**
     * Store a newly created association in storage.
     */
    public function store(Request $request, CreateAssociation $createAssociation): RedirectResponse
    {
        $this->authorize('create', AssociationType::class);

        $createAssociation->create($request->all());

        return redirect()->route('setup.association-type.index')
            ->with('success', 'Association type created successfully.');
    }

    /**
     * Display the specified association.
     */
    public function show(AssociationType $associationType): Response
    {
        $this->authorize('view', $associationType);

        return Inertia::render('dashboard/setup/association-types/Show', [
            'association' => $associationType,
        ]);
    }

    /**
     * Show the form for editing the specified association.
     */
    public function edit(AssociationType $associationType): Response
    {
        $this->authorize('update', $associationType);

        return Inertia::render('dashboard/setup/association-types/Edit', [
            'association' => $associationType,
        ]);
    }

    /**
     * Update the specified association in storage.
     */
    public function update(Request $request, AssociationType $associationType, UpdateAssociation $updateAssociation): RedirectResponse
    {
        $this->authorize('update', $associationType);

        $updateAssociation->update($associationType, $request->all());

        return redirect()->route('setup.association-type.index')
            ->with('success', 'Association type updated successfully.');
    }

    /**
     * Remove the specified association from storage.
     */
    public function destroy(AssociationType $associationType, DeleteAssociation $deleteAssociation): RedirectResponse
    {
        $this->authorize('delete', $associationType);

        $deleteAssociation->delete($associationType);

        return redirect()->route('setup.association-type.index')
            ->with('success', 'Association type deleted successfully.');
    }

    /**
     * Delete multiple association types at once.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $this->authorize('delete', AssociationType::class);

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:association_types,id',
        ]);

        $count = AssociationType::whereIn('id', $validated['ids'])->delete();

        return back()->with('success', "{$count} association type(s) deleted successfully.");
    }

    /**
     * Update status for multiple association types at once.
     */
    public function bulkStatus(Request $request): RedirectResponse
    {
        $this->authorize('update', AssociationType::class);

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:association_types,id',
            'is_active' => 'required|boolean',
        ]);

        $count = AssociationType::whereIn('id', $validated['ids'])
            ->update(['is_active' => $validated['is_active']]);

        $status = $validated['is_active'] ? 'activated' : 'deactivated';

        return back()->with('success', "{$count} association type(s) {$status} successfully.");
    }
}
