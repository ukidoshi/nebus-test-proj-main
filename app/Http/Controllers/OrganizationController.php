<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuildingRequest;
use App\Http\Requests\OrganizationRequest;
use App\Http\Resources\BuildingResource;
use App\Http\Resources\OrganizationResource;
use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Services\OrganizationFilterService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class OrganizationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/organizations/in-building",
     *     tags={"Organizations"},
     *     summary="Список всех организаций находящихся в конкретном здании",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="building_id",
     *          in="query",
     *          description="ID здания",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество результатов на странице",
     *           required=false,
     *           @OA\Schema(type="integer", example=10)
     *       ),
     *      @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="Номер страницы",
     *           required=false,
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *     @OA\Response(response=200, description="Список организаций"),
     * )
     */
    public function inBuilding(Request $request)
    {
        // Валидация входных данных
        $validated = $request->validate([
            'building_id' => 'nullable|integer|exists:buildings,id',
        ]);

        $organizations_filter = new OrganizationFilterService($validated);

        return OrganizationResource::collection($organizations_filter->getOrganizations());
    }

    /**
     * @OA\Get(
     *     path="/organizations/by-activity",
     *     tags={"Organizations"},
     *     summary="Список всех организаций, которые относятся к указанному виду деятельности",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="activity_id",
     *          in="query",
     *          description="ID деятельности",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество результатов на странице (по умолчанию 10)",
     *           required=false,
     *           @OA\Schema(type="integer", example=10)
     *       ),
     *      @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="Номер страницы",
     *           required=false,
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *     @OA\Response(response=200, description="Список организаций"),
     * )
     */
    public function byActivity(Request $request)
    {
        // Валидация входных данных
        $validated = $request->validate([
            'activity_id' => 'nullable|integer|exists:activities,id'
        ]);

        $organizations_filter = new OrganizationFilterService($validated);

        return OrganizationResource::collection($organizations_filter->getOrganizations());
    }

    /**
     * @OA\Get(
     *     path="/organizations/by-radius",
     *     tags={"Organizations"},
     *     summary="Список организаций, которые находятся в заданном радиусе",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="lat",
     *          in="query",
     *          description="Широта",
     *          @OA\Schema(type="float")
     *     ),
     *     @OA\Parameter(
     *          name="lng",
     *          in="query",
     *          description="Долгота",
     *          @OA\Schema(type="float")
     *     ),
     *     @OA\Parameter(
     *          name="radius",
     *          in="query",
     *          description="Радиус поиска",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество результатов на странице (по умолчанию 10)",
     *           required=false,
     *           @OA\Schema(type="integer", example=10)
     *       ),
     *      @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="Номер страницы",
     *           required=false,
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *     @OA\Response(response=200, description="Список организаций"),
     * )
     */
    public function byRadius(Request $request)
    {
        // Валидация входных данных
        $validated = $request->validate([
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:1|max:50000'
        ]);

        $validated['geo_type'] = 'radius';
        $organizations_filter = new OrganizationFilterService($validated);

        return OrganizationResource::collection($organizations_filter->getOrganizations());
    }

    /**
     * @OA\Get(
     *     path="/organizations/by-box",
     *     tags={"Organizations"},
     *     summary="Cписок организаций, которые находятся в заданной прямоугольной области",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *           name="lat",
     *           in="query",
     *           description="Широта",
     *           @OA\Schema(type="float")
     *      ),
     *     @OA\Parameter(
     *           name="lng",
     *           in="query",
     *           description="Долгота",
     *           @OA\Schema(type="float")
     *      ),
     *     @OA\Parameter(
     *           name="lat_2",
     *           in="query",
     *           description="Широта 2",
     *           @OA\Schema(type="float")
     *      ),
     *      @OA\Parameter(
     *           name="lng_2",
     *           in="query",
     *           description="Долгота 2",
     *           @OA\Schema(type="float")
     *      ),
     *     @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество результатов на странице (по умолчанию 10)",
     *           required=false,
     *           @OA\Schema(type="integer", example=10)
     *       ),
     *      @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="Номер страницы",
     *           required=false,
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *     @OA\Response(response=200, description="Список организаций"),
     * )
     */
    public function byBox(Request $request)
    {
        // Валидация входных данных
        $validated = $request->validate([
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'lat_2' => 'nullable|numeric|between:-90,90',
            'lng_2' => 'nullable|numeric|between:-180,180'
        ]);

        $validated['geo_type'] = 'box';
        $organizations_filter = new OrganizationFilterService($validated);

        return OrganizationResource::collection($organizations_filter->getOrganizations());
    }

    /**
     * @OA\Get(
     *     path="/organizations/by-name",
     *     tags={"Organizations"},
     *     summary="Поиск организации по названию",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          description="Поиск",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество результатов на странице (по умолчанию 10)",
     *           required=false,
     *           @OA\Schema(type="integer", example=10)
     *       ),
     *      @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="Номер страницы",
     *           required=false,
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *     @OA\Response(response=200, description="Список организаций"),
     * )
     */
    public function byName(Request $request)
    {
        // Валидация входных данных
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $organizations_filter = new OrganizationFilterService($validated);

        return OrganizationResource::collection($organizations_filter->getOrganizations());
    }

    /**
     * @OA\Get(
     *     path="/organizations",
     *     tags={"Organizations"},
     *     summary="Получить список организаций с всеми фильтрами",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="building_id",
     *          in="query",
     *          description="ID здания",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="activity_id",
     *          in="query",
     *          description="ID деятельности",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="geo_type",
     *          in="query",
     *          description="Тип гео-поиска (принимает значения 'box' или 'radius')",
     *          required=false,
     *          @OA\Schema(type="string", format="string", example="radius")
     *     ),
     *     @OA\Parameter(
     *          name="lat",
     *          in="query",
     *          description="Широта, если указан geo_type 'box' или 'radius'",
     *          required=false,
     *          @OA\Schema(type="number", format="float", example=55.7558)
     *      ),
     *     @OA\Parameter(
     *          name="lng",
     *          in="query",
     *          description="Долгота, если указан geo_type 'box' или 'radius'",
     *          required=false,
     *          @OA\Schema(type="number", format="float", example=37.6173)
     *      ),
     *     @OA\Parameter(
     *           name="lat_2",
     *           in="query",
     *           description="Широта второй точки, если указан geo_type 'box'",
     *           required=false,
     *           @OA\Schema(type="number", format="float", example=55.7558)
     *       ),
     *      @OA\Parameter(
     *           name="lng_2",
     *           in="query",
     *           description="Долгота второй точки, если указан geo_type 'box'",
     *           required=false,
     *           @OA\Schema(type="number", format="float", example=37.6173)
     *       ),
     *      @OA\Parameter(
     *           name="radius",
     *           in="query",
     *           description="Радиус поиска в метрах (по умолчанию 1000м), если указан geo_type 'radius'",
     *           required=false,
     *           @OA\Schema(type="integer", example=1000)
     *       ),
     *      @OA\Parameter(
     *           name="search",
     *           in="query",
     *           description="Поисковый запрос по названию организации",
     *           required=false,
     *           @OA\Schema(type="string", example="кафе")
     *       ),
     *      @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество результатов на странице (по умолчанию 10)",
     *           required=false,
     *           @OA\Schema(type="integer", example=10)
     *       ),
     *      @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="Номер страницы",
     *           required=false,
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *     @OA\Response(response=200, description="Список организаций"),
     * )
     */
    public function index(Request $request)
    {
        // Валидация входных данных
        $validated = $request->validate([
            'building_id' => 'nullable|integer|exists:buildings,id',
            'activity_id' => 'nullable|integer|exists:activities,id',
            'geo_type' => ['nullable', Rule::in(['box', 'radius'])],
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'lat_2' => 'nullable|numeric|between:-90,90',
            'lng_2' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:1|max:50000',
            'search' => 'nullable|string|max:255',
        ]);

        $organizations_filter = new OrganizationFilterService($validated);

        return OrganizationResource::collection($organizations_filter->getOrganizations());
    }

    /**
     * @OA\Get(
     *     path="/organizations/{id}",
     *     tags={"Organizations"},
     *     summary="Получить информацию об организации по ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID организации",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Информация об организации", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Организация не найдена"),
     * )
     */
    public function show($id)
    {
        $organization = Organization::with(['building', 'activities', 'phone_numbers'])->find($id);

        if (!$organization) {
            return response()->json(['message' => 'Организация не найдена'], 404);
        }

        return new OrganizationResource($organization);
    }

    /**
     * @OA\Post(
     *     path="/organizations",
     *     tags={"Organizations"},
     *     summary="Добавить новую организацию",
     *     security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *               required={"building_id", "name"},
     *               @OA\Property(property="building_id", type="integer", example="1"),
     *               @OA\Property(property="name", type="string", example="АНО Живи"),
     *          )
     *      ),
     *     @OA\Response(response=201, description="Организация создана", @OA\JsonContent())
     * )
     */
    public function store(OrganizationRequest $request)
    {
        $validated = $request->validated();

        $organization = Organization::create($validated);
        return new OrganizationResource($organization);
    }

    /**
     * @OA\Put(
     *     path="/organizations/{id}",
     *     tags={"Organizations"},
     *     summary="Обновить данные об организации",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"building_id", "name"},
     *              @OA\Property(property="building_id", type="integer", example="1"),
     *              @OA\Property(property="name", type="string", example="ООО Металстрой"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Организация обновлена", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Организация не найдена")
     * )
     */
    public function update(OrganizationRequest $request)
    {
        $validated = $request->validated();

        try {
            $organization = Organization::findOrFail($request->id ?? null);
        } catch (\Exception $e) {
            // model not found
            return response()->json(['message' => 'Организация не найдена'], 404);
        }

        $organization->update($validated);

        return new OrganizationResource($organization);
    }

    /**
     * @OA\Delete(
     *     path="/organizations/{id}",
     *     tags={"Organizations"},
     *     summary="Удалить организацию",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Организация удалена", @OA\JsonContent())
     * )
     */
    public function destroy(Request $request)
    {
        Organization::find($request->id ?? null)?->delete();
        return response()->json(['message' => 'Удалено']);
    }
}
