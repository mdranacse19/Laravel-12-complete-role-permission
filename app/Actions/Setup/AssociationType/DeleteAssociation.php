<?php

namespace App\Actions\Setup\AssociationType;

use App\Models\Setup\AssociationType;

class DeleteAssociation
{
    /**
     * Delete an association.
     *
     * @param  AssociationType  $associationType
     * @return bool
     */
    public function delete(AssociationType $associationType): bool
    {
        return $associationType->delete();
    }
}
