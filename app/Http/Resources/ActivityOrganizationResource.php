<?php

namespace App\Http\Resources;

use App\Models\ActivityOrganization;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ActivityOrganization */
class ActivityOrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'activity_id' => $this->activity_id,
            'organization_id' => $this->organization_id,
        ];
    }
}
