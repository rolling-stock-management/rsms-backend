<?php

namespace App\Http\Controllers;

use App\Http\Requests\TractiveUnitStoreRequest;
use App\Http\Requests\TractiveUnitUpdateRequest;
use App\Http\Resources\TractiveUnitResource;
use App\Models\TractiveUnit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TractiveUnitController extends Controller
{
    /**
     * TractiveUnitController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(TractiveUnit::class, 'tractive_unit');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $tractiveUnits = TractiveUnit::paginate(10);

        return TractiveUnitResource::collection($tractiveUnits);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TractiveUnitStoreRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(TractiveUnitStoreRequest $request)
    {
        $tractiveUnit = TractiveUnit::create($request->validated());

        return (new TractiveUnitResource($tractiveUnit))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TractiveUnit  $tractiveUnit
     * @return TractiveUnitResource|\Illuminate\Http\Response
     */
    public function show(TractiveUnit $tractiveUnit)
    {
        return new TractiveUnitResource($tractiveUnit);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TractiveUnitUpdateRequest $request
     * @param \App\Models\TractiveUnit $tractiveUnit
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(TractiveUnitUpdateRequest $request, TractiveUnit $tractiveUnit)
    {
        $tractiveUnit->update($request->validated());

        return (new TractiveUnitResource($tractiveUnit))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TractiveUnit  $tractiveUnit
     * @return \Illuminate\Http\Response
     */
    public function destroy(TractiveUnit $tractiveUnit)
    {
        $tractiveUnit->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }
}
