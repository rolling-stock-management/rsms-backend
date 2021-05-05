<?php

namespace App\Http\Controllers;

use App\Http\Resources\RollingStockTrainResource;
use App\Models\FreightWagon;
use App\Models\PassengerWagon;
use App\Models\RollingStockTrain;
use App\Models\TractiveUnit;
use App\Rules\ExistsRollingStockTrainable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RollingStockTrainController extends Controller
{
    /**
     * RollingStockTrainController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(RollingStockTrain::class, 'rolling-stock-train');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rollingStockTrains = RollingStockTrain::paginate(10);

        return RollingStockTrainResource::collection($rollingStockTrains);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validateRequestData();
        $rollingStockTrain = RollingStockTrain::create($data);

        $rollingStockTrainable = null;
        switch ($data['trainable_type']) {
            case 1:
            {
                $rollingStockTrainable = PassengerWagon::find($data['trainable_id']);
                break;
            }
            case 2:
            {
                $rollingStockTrainable = FreightWagon::find($data['trainable_id']);
                break;
            }
            case 3:
            {
                $rollingStockTrainable = TractiveUnit::find($data['trainable_id']);
                break;
            }
        }
        $rollingStockTrainable->repairs()->save($rollingStockTrain);

        return (new RollingStockTrainResource($rollingStockTrain))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RollingStockTrain  $rollingStockTrain
     * @return \Illuminate\Http\Response
     */
    public function show(RollingStockTrain $rollingStockTrain)
    {
        return new RollingStockTrainResource($rollingStockTrain);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RollingStockTrain  $rollingStockTrain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RollingStockTrain $rollingStockTrain)
    {
        $data = $this->validateRequestData();
        unset($data['trainable_type']);
        $rollingStockTrain->update($data);

        return (new RollingStockTrainResource($rollingStockTrain))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RollingStockTrain  $rollingStockTrain
     * @return \Illuminate\Http\Response
     */
    public function destroy(RollingStockTrain $rollingStockTrain)
    {
        $rollingStockTrain->delete();

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
            'type_id' => ['required', 'integer', 'exists:App\Models\RollingStockTrainType,id'],
            'workshop_id' => ['required', 'integer', 'exists:App\Models\RollingStockTrainWorkshop,id'],
            'description' => ['sometimes', 'string', 'nullable'],
            'trainable_type' => ['required', Rule::in([1,2,3])],
            'trainable_id' => ['required', new ExistsRollingStockTrainable],
            'start_date' => ['required', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date']
        ]);
    }
}
