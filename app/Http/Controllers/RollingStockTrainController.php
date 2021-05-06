<?php

namespace App\Http\Controllers;

use App\Http\Resources\RollingStockTrainResource;
use App\Models\FreightWagon;
use App\Models\PassengerWagon;
use App\Models\RollingStockTrain;
use App\Models\TractiveUnit;
use App\Rules\ExistsTrainable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RollingStockTrainController extends Controller
{
    /**
     * RollingStockTrainController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(RollingStockTrain::class, 'rolling_stock_train');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $rollingStockTrains = RollingStockTrain::allRollingStockTrains();

        return RollingStockTrainResource::collection($rollingStockTrains);
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
        $data = array_merge($data, ['user_id' => Auth::user()->id]);
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
        $rollingStockTrainable->trains()->save($rollingStockTrain);

        return (new RollingStockTrainResource($rollingStockTrain))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RollingStockTrain  $rollingStockTrain
     * @return RollingStockTrainResource|\Illuminate\Http\Response
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
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
            'date' => ['required', 'date'],
            'position' => ['required', 'integer'],
            'train_id' => ['required', 'integer', 'exists:App\Models\Train,id'],
            'comment' => ['sometimes', 'string', 'nullable'],
            'trainable_type' => ['required', Rule::in([1,2,3])],
            'trainable_id' => ['required', new ExistsTrainable],
        ]);
    }
}
