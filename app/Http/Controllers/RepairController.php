<?php

namespace App\Http\Controllers;

use App\Http\Resources\RepairResource;
use App\Models\FreightWagon;
use App\Models\PassengerWagon;
use App\Models\Repair;
use App\Models\TractiveUnit;
use App\Rules\ExistsRepairable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RepairController extends Controller
{
    /**
     * RepairController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Repair::class, 'repair');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $repairs = Repair::allRepairs();

        return RepairResource::collection($repairs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $data = $this->validateRequestData();
        $repair = Repair::create($data);

        $repairable = null;
        switch ($data['repairable_type']) {
            case 1:
            {
                $repairable = PassengerWagon::find($data['repairable_id']);
                break;
            }
            case 2:
            {
                $repairable = FreightWagon::find($data['repairable_id']);
                break;
            }
            case 3:
            {
                $repairable = TractiveUnit::find($data['repairable_id']);
                break;
            }
        }
        $repairable->repairs()->save($repair);

        return (new RepairResource($repair))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Repair  $repair
     * @return RepairResource|\Illuminate\Http\Response
     */
    public function show(Repair $repair)
    {
        return new RepairResource($repair);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Repair  $repair
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, Repair $repair)
    {
        $data = $this->validateRequestData();
        unset($data['repairable_type']);
        $repair->update($data);

        return (new RepairResource($repair))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Repair  $repair
     * @return \Illuminate\Http\Response
     */
    public function destroy(Repair $repair)
    {
        $repair->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Validate data from the request.
     *
     * @return mixed
     */
    private function validateRequestData()
    {
        return request()->validate([
            'short_description' => ['required', 'string'],
            'type_id' => ['required', 'integer', 'exists:App\Models\RepairType,id'],
            'workshop_id' => ['required', 'integer', 'exists:App\Models\RepairWorkshop,id'],
            'description' => ['sometimes', 'string', 'nullable'],
            'repairable_type' => ['required', Rule::in([1,2,3])],
            'repairable_id' => ['required', new ExistsRepairable],
            'start_date' => ['required', 'date'],
            'end_date' => ['sometimes', 'date']
        ]);
    }
}
