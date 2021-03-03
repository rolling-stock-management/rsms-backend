<?php

namespace App\Http\Controllers;

use App\Http\Resources\PassengerInteriorTypeResource;
use App\Models\PassengerInteriorType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PassengerInteriorTypeController extends Controller
{
    /**
     * PassengerInteriorTypeController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(PassengerInteriorType::class, 'passenger_interior_type');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('no-pagination') && request('no-pagination')) {
            $passengerInteriorType = PassengerInteriorType::all();
        }
        else
        {
            $passengerInteriorType = PassengerInteriorType::paginate(10);
        }

        return PassengerInteriorTypeResource::collection($passengerInteriorType);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $passengerInteriorType = PassengerInteriorType::create($this->validateRequestData());

        return (new PassengerInteriorTypeResource($passengerInteriorType))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PassengerInteriorType  $passengerInteriorType
     * @return PassengerInteriorTypeResource|\Illuminate\Http\Response
     */
    public function show(PassengerInteriorType $passengerInteriorType)
    {
        return new PassengerInteriorTypeResource($passengerInteriorType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PassengerInteriorType  $passengerInteriorType
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, PassengerInteriorType $passengerInteriorType)
    {
        $passengerInteriorType->update($this->validateRequestData());

        return (new PassengerInteriorTypeResource($passengerInteriorType))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PassengerInteriorType  $passengerInteriorType
     * @return \Illuminate\Http\Response
     */
    public function destroy(PassengerInteriorType $passengerInteriorType)
    {
        $passengerInteriorType->delete();

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
            'description' => ['sometimes', 'string', 'nullable']
        ]);
    }
}
