<?php

namespace App\Http\Resources\Dashboard\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StakeholderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'typeName' => $this->type->label(),
            'typeTag' => $this->type->tag(),
            'name' => $this->name,
            'bnName' => $this->bn_name,
            'designation' => $this->designation,
            'bnDesignation' => $this->bn_designation,
            'mobileNo' => $this->mobile,
            'email' => $this->email,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
