<?php

namespace App\Http\Controllers;

use App\Http\Requests\FreightWagonStoreRequest;
use App\Http\Requests\FreightWagonUpdateRequest;
use App\Http\Resources\FreightWagonResource;
use App\Models\FreightWagon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FreightWagonController extends Controller
{
    /**
     * FreightWagonController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(FreightWagon::class, 'freight_wagon');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $freightWagons = FreightWagon::paginate(10);

        return FreightWagonResource::collection($freightWagons);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FreightWagonStoreRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(FreightWagonStoreRequest $request)
    {
        $freightWagon = FreightWagon::create($request->validated());

        return (new FreightWagonResource($freightWagon))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FreightWagon $freightWagon
     * @return FreightWagonResource|\Illuminate\Http\Response
     */
    public function show(Request $request, FreightWagon $freightWagon)
    {
        return new FreightWagonResource($freightWagon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FreightWagonUpdateRequest $request
     * @param \App\Models\FreightWagon $freightWagon
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(FreightWagonUpdateRequest $request, FreightWagon $freightWagon)
    {
        $freightWagon->update($request->validated());

        return (new FreightWagonResource($freightWagon))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FreightWagon  $freightWagon
     * @return \Illuminate\Http\Response
     */
    public function destroy(FreightWagon $freightWagon)
    {
        $freightWagon->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }
}
