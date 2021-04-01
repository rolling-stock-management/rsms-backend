<?php

namespace App\Http\Controllers;

use App\Http\Resources\RepairResource;
use App\Models\FreightWagon;
use App\Models\PassengerWagon;
use App\Models\Repair;
use App\Models\TractiveUnit;
use Illuminate\Http\Request;
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
        $repair = Repair::paginate(10);

        return RepairResource::collection($repair);
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

        //TODO: Check refactoring options.
        if (array_key_exists('passenger_wagon_id',$data)) {
            $passengerWagon = PassengerWagon::find($data['passenger_wagon_id']);
            $passengerWagon->repairs()->save($repair);
        }
        if (array_key_exists('freight_wagon_id',$data)) {
            $freightWagon = FreightWagon::find($data['freight_wagon_id']);
            $freightWagon->repairs()->save($repair);
        }
        if (array_key_exists('tractive_unit_id',$data)) {
            $tractiveUnit = TractiveUnit::find($data['tractive_unit_id']);
            $tractiveUnit->repairs()->save($repair);
        }

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
        $repair->update($data);

        //TODO: Check refactoring options.
        $repairableId = 0;
        if (array_key_exists('passenger_wagon_id',$data)) {
            $repairableId = $data['passenger_wagon_id'];
        }
        if (array_key_exists('freight_wagon_id',$data)) {
            $repairableId = $data['freight_wagon_id'];
        }
        if (array_key_exists('tractive_unit_id',$data)) {
            $repairableId = $data['tractive_unit_id'];
        }

        $repair->update(['repairable_id' => $repairableId]);

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
            'passenger_wagon_id' => ['exclude_unless:freight_wagon_id,null', 'exclude_unless:tractive_unit_id,null', 'required', 'integer', 'exists:App\Models\PassengerWagon,id'],
            'freight_wagon_id' => ['exclude_unless:passenger_wagon_id,null', 'exclude_unless:tractive_unit_id,null', 'required', 'integer', 'exists:App\Models\FreightWagon,id'],
            'tractive_unit_id' => ['exclude_unless:passenger_wagon_id,null', 'exclude_unless:freight_wagon_id,null', 'required', 'integer', 'exists:App\Models\TractiveUnit,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['sometimes', 'date']
        ]);
    }
}
