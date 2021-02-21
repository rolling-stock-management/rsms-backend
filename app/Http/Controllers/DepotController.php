<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepotResource;
use App\Models\Depot;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepotController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Depot::class, 'depot');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $depot = Depot::all();
        return DepotResource::collection($depot);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $depot = Depot::create($this->validateRequestData());
        return (new DepotResource($depot))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Depot  $depot
     * @return \Illuminate\Http\Response
     */
    public function show(Depot $depot)
    {
        return new DepotResource($depot);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Depot  $depot
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, Depot $depot)
    {
        $depot->update($this->validateRequestData());
        return (new DepotResource($depot))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Depot  $depot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Depot $depot)
    {
        $depot->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Validate data from request.
     *
     * @return mixed
     */
    private function validateRequestData()
    {
        return request()->validate([
            'name' => 'required|string',
            'note' => 'sometimes|required|string'
        ]);
    }
}
