<?php

namespace App\Actions\Profile\Stakeholder;

use App\Models\Profile\Stakeholder;

class DeleteStakeholder
{
    /**
     * Delete a stakeholder.
     *
     * @param  Stakeholder  $stakeholder
     * @return bool
     */
    public function delete(Stakeholder $stakeholder): bool
    {
        return $stakeholder->delete();
    }
}
