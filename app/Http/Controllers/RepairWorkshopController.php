<?php

namespace App\Http\Controllers;

use App\Http\Resources\RepairWorkshopResource;
use App\Models\RepairWorkshop;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RepairWorkshopController extends Controller
{
    /**
     * RepairWorkshopController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(RepairWorkshop::class, 'repair_workshop');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('no-pagination') && request('no-pagination')) {
            $repairWorkshops = RepairWorkshop::all();
        }
        else
        {
            $repairWorkshops = RepairWorkshop::paginate(10);
        }
        return RepairWorkshopResource::collection($repairWorkshops);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $repairWorkshop = RepairWorkshop::create($this->validateRequestData());

        return (new RepairWorkshopResource($repairWorkshop))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RepairWorkshop  $repairWorkshop
     * @return RepairWorkshopResource|\Illuminate\Http\Response
     */
    public function show(RepairWorkshop $repairWorkshop)
    {
        return new RepairWorkshopResource($repairWorkshop);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RepairWorkshop  $repairWorkshop
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, RepairWorkshop $repairWorkshop)
    {
        $repairWorkshop->update($this->validateRequestData());

        return (new RepairWorkshopResource($repairWorkshop))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RepairWorkshop  $repairWorkshop
     * @return \Illuminate\Http\Response
     */
    public function destroy(RepairWorkshop $repairWorkshop)
    {
        $repairWorkshop->delete();

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
            'abbreviation' => ['required', 'string'],
            'note' => ['sometimes', 'string']
        ]);
    }
}
