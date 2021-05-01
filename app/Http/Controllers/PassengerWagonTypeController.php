<?php

namespace App\Http\Controllers;

use App\Http\Resources\PassengerWagonTypeResource;
use App\Models\PassengerWagonType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PassengerWagonTypeController extends Controller
{
    /**
     * PassengerWagonTypeController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(PassengerWagonType::class, 'passenger_wagon_type');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('no-pagination') && request('no-pagination')) {
            $passengerWagonType = PassengerWagonType::all();
        }
        else
        {
            $passengerWagonType = PassengerWagonType::paginate(10);
        }

        return PassengerWagonTypeResource::collection($passengerWagonType);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $passengerWagonType = PassengerWagonType::create($this->validateRequestData());

        return (new PassengerWagonTypeResource($passengerWagonType))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PassengerWagonType  $passengerWagonType
     * @return PassengerWagonTypeResource|\Illuminate\Http\Response
     */
    public function show(PassengerWagonType $passengerWagonType)
    {
        return new PassengerWagonTypeResource($passengerWagonType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PassengerWagonType  $passengerWagonType
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, PassengerWagonType $passengerWagonType)
    {
        $passengerWagonType->update($this->validateRequestData());

        return (new PassengerWagonTypeResource($passengerWagonType))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PassengerWagonType  $passengerWagonType
     * @return \Illuminate\Http\Response
     */
    public function destroy(PassengerWagonType $passengerWagonType)
    {
        $passengerWagonType->delete();

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
            'interior_type_id' => ['required', 'integer', 'exists:App\Models\PassengerInteriorType,id'],
            'repair_valid_for' => ['required', 'integer', 'gt:0']
        ]);
    }
}
