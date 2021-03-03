<?php

namespace App\Http\Controllers;

use App\Http\Resources\RepairTypeResource;
use App\Models\RepairType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RepairTypeController extends Controller
{
    /**
     * RepairTypeController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(RepairType::class, 'repair_type');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('no-pagination') && request('no-pagination')) {
            $repairTypes = RepairType::all();
        }
        else
        {
            $repairTypes = RepairType::paginate(10);
        }
        return RepairTypeResource::collection($repairTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $repairType = RepairType::create($this->validateRequestData());

        return (new RepairTypeResource($repairType))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RepairType  $repairType
     * @return RepairTypeResource|\Illuminate\Http\Response
     */
    public function show(RepairType $repairType)
    {
        return new RepairTypeResource($repairType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RepairType  $repairType
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, RepairType $repairType)
    {
        $repairType->update($this->validateRequestData());

        return (new RepairTypeResource($repairType))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RepairType  $repairType
     * @return \Illuminate\Http\Response
     */
    public function destroy(RepairType $repairType)
    {
        $repairType->delete();
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
