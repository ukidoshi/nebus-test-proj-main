<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityOrganizationResource;
use App\Http\Resources\OrganizationResource;
use App\Models\Activity;
use App\Models\ActivityOrganization;
use App\Models\Organization;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Organizations-activities")
 */
class OrganizationActivityController extends Controller
{
    /**
     * @OA\Post(
     *     path="/organizations/{id}/activities/attach",
     *     tags={"Organizations"},
     *     summary="Привязка видов деятельности к организациям",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID организации",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *           required=true,
     *           @OA\JsonContent(
     *                required={"activity_id"},
     *                @OA\Property(property="activity_id", type="integer", example="1", description="ID деятельности")
     *           )
     *       ),
     *     @OA\Response(response=200, description="Информация об организации", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Организация не найдена"),
     *     @OA\Response(response=422, description="Деятельность уже привязана к организации"),
     * )
     */
    public function attach(Request $request)
    {
        $validate = $request->validate([
            'activity_id' => ['required', 'integer']
        ]);

        try {
            $organization = Organization::findOrFail($request->id ?? null);
        } catch (\Exception $e) {
            // model not found
            return response()->json(['message' => 'Организация не найдена'], 404);
        }

        try {
            $activity = Activity::findOrFail($validate['activity_id'] ?? null);
        } catch (\Exception $e) {
            // model not found
            return response()->json(['message' => 'Вид деятельности не найдена'], 404);
        }

        if ($organization->hasActivity($activity)) {
            return response()->json(['message' => 'Такой вид деятельности уже есть у организации'], 422);
        }

        ActivityOrganization::create([
            'organization_id' => $organization->id,
            'activity_id' => $activity->id,
        ]);

        return new OrganizationResource($organization);
    }
}

