<?php

namespace App\Http\Controllers\Dashboard\Profile;

use App\Actions\Profile\Stakeholder\CreateStakeholder;
use App\Actions\Profile\Stakeholder\DeleteStakeholder;
use App\Actions\Profile\Stakeholder\UpdateStakeholder;
use App\Enums\StakeholderType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\Profile\StakeholderResource;
use App\Models\Profile\Stakeholder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StakeholderController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of Stakeholders.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Stakeholder::class);

        $query = Stakeholder::query();

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
        $stakeholders = $query->paginate($perPage);

        $types = StakeholderType::options();

        return Inertia::render('dashboard/profile/stakeholder/Index', [
            'Stakeholders' => StakeholderResource::collection($stakeholders),
            'filters' => $request->only(['search', 'order_by', 'order_direction']),
            'types' => $types,
        ]);
    }

    /**
     * Store a newly created Stakeholder in storage.
     */
    public function store(Request $request, CreateStakeholder $createStakeholder): RedirectResponse
    {
        $this->authorize('create', Stakeholder::class);

        $createStakeholder->create($request->all());

        return redirect()->route('profile.stakeholders.index')
            ->with('success', 'Stakeholder type created successfully.');
    }

    /**
     * Update the specified Stakeholder in storage.
     */
    public function update(Request $request, Stakeholder $stakeholder, UpdateStakeholder $updateStakeholder): RedirectResponse
    {
        $this->authorize('update', $stakeholder);

        $updateStakeholder->update($stakeholder, $request->all());

        return redirect()->route('profile.stakeholders.index')
            ->with('success', 'Stakeholder type updated successfully.');
    }

    /**
     * Remove the specified Stakeholder from storage.
     */
    public function destroy(Stakeholder $stakeholder, DeleteStakeholder $deleteStakeholder): RedirectResponse
    {
        $this->authorize('delete', $stakeholder);

        $deleteStakeholder->delete($stakeholder);

        return redirect()->route('profile.stakeholders.index')
            ->with('success', 'Stakeholder type deleted successfully.');
    }

    /**
     * Delete multiple Stakeholder types at once.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $this->authorize('delete', Stakeholder::class);

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:Stakeholder_types,id',
        ]);

        $count = Stakeholder::whereIn('id', $validated['ids'])->delete();

        return back()->with('success', "{$count} Stakeholder type(s) deleted successfully.");
    }

    /**
     * Update status for multiple Stakeholder types at once.
     */
    public function bulkStatus(Request $request): RedirectResponse
    {
        $this->authorize('update', Stakeholder::class);

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:Stakeholder_types,id',
            'is_active' => 'required|boolean',
        ]);

        $count = Stakeholder::whereIn('id', $validated['ids'])
            ->update(['is_active' => $validated['is_active']]);

        $status = $validated['is_active'] ? 'activated' : 'deactivated';

        return back()->with('success', "{$count} Stakeholder type(s) {$status} successfully.");
    }
}
