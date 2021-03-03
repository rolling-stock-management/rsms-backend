<?php

namespace App\Http\Controllers;

use App\Http\Requests\PassengerWagonStoreRequest;
use App\Http\Requests\PassengerWagonUpdateRequest;
use App\Http\Resources\PassengerWagonResource;
use App\Models\PassengerWagon;
use Symfony\Component\HttpFoundation\Response;

class PassengerWagonController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PassengerWagon::class, 'passenger_wagon');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $passengerWagons = PassengerWagon::paginate(10);

        return PassengerWagonResource::collection($passengerWagons);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PassengerWagonStoreRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(PassengerWagonStoreRequest $request)
    {
        $passengerWagon = PassengerWagon::create($request->validated());

        return (new PassengerWagonResource($passengerWagon))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PassengerWagon  $passengerWagon
     * @return PassengerWagonResource|\Illuminate\Http\Response
     */
    public function show(PassengerWagon $passengerWagon)
    {
        return new PassengerWagonResource($passengerWagon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PassengerWagonUpdateRequest $request
     * @param \App\Models\PassengerWagon $passengerWagon
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(PassengerWagonUpdateRequest $request, PassengerWagon $passengerWagon)
    {
        $passengerWagon->update($request->validated());

        return (new PassengerWagonResource($passengerWagon))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PassengerWagon  $passengerWagon
     * @return \Illuminate\Http\Response
     */
    public function destroy(PassengerWagon $passengerWagon)
    {
        $passengerWagon->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }
}
