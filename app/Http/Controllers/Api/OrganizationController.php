<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Organization\SearchByActivityRequest;
use App\Http\Requests\Api\Organization\SearchByGeoRequest;
use App\Http\Requests\Api\Organization\SearchByNameRequest;
use App\Http\Resources\Api\Building\BuildingResource;
use App\Http\Resources\Api\Organization\OrganizationResource;
use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Database\PostgisFunctions\ST;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/organizations/{organization}",
     *     summary="Данные по организации",
     *     description="Возвращает данные организации по ID",
     *     operationId="getOrganizationById",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="organization",
     *         in="path",
     *         required=true,
     *         description="ID организации",
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Organization")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *             @OA\Property(
     *              property="data",
     *              type="object",
     *              @OA\Property(property="error", type="string", example="Unauthorized")
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Entity not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *               property="data",
     *               type="object",
     *              @OA\Property(property="error", type="string", example="Entity not found")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function show(Organization $organization): OrganizationResource
    {
        return OrganizationResource::make($organization);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/search/name",
     *     summary="Поиск организации по имени",
     *     description="Поиск организации по имени или части имени без учета регистра",
     *     operationId="searchOrganizationsByName",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="Имя организации или часть имени",
     *         @OA\Schema(
     *             type="string",
     *             example="ОАО Компания"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Страница пагинации",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Organization")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://localhost/api/organizations/search-by-name?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://localhost/api/organizations/search-by-name?page=5"),
     *                 @OA\Property(property="prev", type="string", example="http://localhost/api/organizations/search-by-name?page=2"),
     *                 @OA\Property(property="next", type="string", example="http://localhost/api/organizations/search-by-name?page=4")
     *             ),
     *     @OA\Property(
     *      property="meta",
     *      type="object",
     *      @OA\Property(property="current_page", type="integer", example=1),
     *      @OA\Property(property="from", type="integer", example=1),
     *      @OA\Property(property="last_page", type="integer", example=1),
     *      @OA\Property(
     *          property="links",
     *          type="array",
     *          @OA\Items(
     *              type="object",
     *              @OA\Property(property="url", type="string", nullable=true, example=null),
     *              @OA\Property(property="label", type="string", example="pagination.previous"),
     *              @OA\Property(property="page", type="integer", nullable=true, example=null),
     *              @OA\Property(property="active", type="boolean", example=false)
     *          ),
     *          example={
     *              {
     *                  "url": null,
     *                  "label": "pagination.previous",
     *                  "page": null,
     *                  "active": false
     *              },
     *              {
     *                  "url": "http://localhost:8080/api/organizations/search/name?page=1",
     *                  "label": "1",
     *                  "page": 1,
     *                  "active": true
     *              },
     *              {
     *                  "url": null,
     *                  "label": "pagination.next",
     *                  "page": null,
     *                  "active": false
     *              }
     *          }
     *      ),
     *      @OA\Property(property="path", type="string", example="http://localhost:8080/api/organizations/search/name"),
     *      @OA\Property(property="per_page", type="integer", example=100),
     *      @OA\Property(property="to", type="integer", example=25),
     *      @OA\Property(property="total", type="integer", example=25)
     *  )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *             @OA\Property(
     *              property="data",
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function searchByName(SearchByNameRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $validated = $request->validated();
        $organizations = Organization::with('building')
            ->where('name', 'ilike', '%' . $validated['name'] . '%')
            ->paginate(100);
        return OrganizationResource::collection($organizations);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/search/activity/{activity}",
     *     summary="Поиск организаций по виду деятельности",
     *     description="Возвращает организации, связанные с указанным видом деятельности и всеми его дочерними видами",
     *     operationId="searchOrganizationsByActivity",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="activity",
     *         in="path",
     *         required=true,
     *         description="ID вида деятельности",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Organization")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="error", type="string", example="Unauthorized")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entity not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="error", type="string", example="Entity not found")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="error", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */
    public function searchByActivity(Activity $activity): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $allActivities = $activity->getAllDescendantsAndSelf();
        $activitiesIds = $allActivities->pluck('id');

        $organizations = Organization::query()->whereHas('activities', function ($query) use ($activitiesIds) {
            $query->whereIn('activities.id', $activitiesIds);
        })->get();

        return OrganizationResource::collection($organizations);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/search/geo",
     *     summary="Поиск организаций по географическим координатам",
     *     description="Поиск организаций в заданном радиусе или прямоугольной области. Необходимо указать либо все параметры для поиска по радиусу, либо все параметры для поиска по прямоугольной области.",
     *     operationId="searchOrganizationsByGeo",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         required=false,
     *         description="Широта центра поиска (обязателен вместе с lng и radius)",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=55.755826
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lng",
     *         in="query",
     *         required=false,
     *         description="Долгота центра поиска (обязателен вместе с lat и radius)",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=37.617634
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         required=false,
     *         description="Радиус поиска в метрах (обязателен вместе с lat и lng)",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=1000,
     *             minimum=0
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="min_lat",
     *         in="query",
     *         required=false,
     *         description="Минимальная широта прямоугольной области (обязателен вместе с min_lng, max_lat и max_lng)",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=55.7,
     *             minimum=-90,
     *             maximum=90
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="min_lng",
     *         in="query",
     *         required=false,
     *         description="Минимальная долгота прямоугольной области (обязателен вместе с min_lat, max_lat и max_lng)",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=37.6,
     *             minimum=-180,
     *             maximum=180
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="max_lat",
     *         in="query",
     *         required=false,
     *         description="Максимальная широта прямоугольной области (обязателен вместе с min_lat, min_lng и max_lng)",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=55.8,
     *             minimum=-90,
     *             maximum=90
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="max_lng",
     *         in="query",
     *         required=false,
     *         description="Максимальная долгота прямоугольной области (обязателен вместе с min_lat, min_lng и max_lat)",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=37.7,
     *             minimum=-180,
     *             maximum=180
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Building")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="parameters",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="Необходимо указать либо все параметры для поиска по радиусу (lat, lng, radius), либо все параметры для поиска по прямоугольной области (min_lat, min_lng, max_lat, max_lng)"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="lat",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="Широта обязательна при указании долготы и радиуса"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="min_lat",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="Минимальная широта обязательна при указании границ области"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="error", type="string", example="Unauthorized")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="error", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */
    public function searchByGeo(SearchByGeoRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $validated = $request->validated();
        $query = Building::with('organizations');

        if (isset($validated['lat']) && isset($validated['lng']) && isset($validated['radius'])) {
            $query->withinRadius(
                $validated['lat'],
                $validated['lng'],
                $validated['radius']
            );
        }

        if (isset($validated['min_lat']) && isset($validated['min_lng']) &&
            isset($validated['max_lat']) && isset($validated['max_lng'])) {
            $query->withinBoundingBox(
                $validated['min_lat'],
                $validated['min_lng'],
                $validated['max_lat'],
                $validated['max_lng']
            );
        }

        return BuildingResource::collection($query->get());
    }
}
