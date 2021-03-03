<?php

namespace App\Http\Controllers;

use App\Http\Resources\FreightWagonTypeResource;
use App\Models\FreightWagonType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FreightWagonTypeController extends Controller
{
    /**
     * FreightWagonTypeController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(FreightWagonType::class, 'freight_wagon_type');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('no-pagination') && request('no-pagination')) {
            $freightWagonType = FreightWagonType::all();
        }
        else
        {
            $freightWagonType = FreightWagonType::paginate(10);
        }

        return FreightWagonTypeResource::collection($freightWagonType);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $freightWagonType = FreightWagonType::create($this->validateRequestData());

        return (new FreightWagonTypeResource($freightWagonType))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FreightWagonType  $freightWagonType
     * @return FreightWagonTypeResource|\Illuminate\Http\Response
     */
    public function show(FreightWagonType $freightWagonType)
    {
        return new FreightWagonTypeResource($freightWagonType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FreightWagonType  $freightWagonType
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, FreightWagonType $freightWagonType)
    {
        $freightWagonType->update($this->validateRequestData());

        return (new FreightWagonTypeResource($freightWagonType))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FreightWagonType  $freightWagonType
     * @return \Illuminate\Http\Response
     */
    public function destroy(FreightWagonType $freightWagonType)
    {
        $freightWagonType->delete();

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
            'name' => ['required', 'string'],
            'description' => ['sometimes', 'string', 'nullable'],
        ]);
    }
}
