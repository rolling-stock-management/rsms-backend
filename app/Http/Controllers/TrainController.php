<?php

namespace App\Http\Controllers;

use App\Http\Resources\TrainResource;
use App\Models\Train;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrainController extends Controller
{
    /**
     * TrainController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Train::class, 'train');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $trains = Train::paginate(10);
        return TrainResource::collection($trains);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $train = Train::create($this->validateRequestData());

        return (new TrainResource($train))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Train  $train
     * @return TrainResource|\Illuminate\Http\Response
     */
    public function show(Train $train)
    {
        return new TrainResource($train);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Train  $train
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, Train $train)
    {
        $train->update($this->validateRequestData());

        return (new TrainResource($train))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Train  $train
     * @return \Illuminate\Http\Response
     */
    public function destroy(Train $train)
    {
        $train->delete();
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
            'number' => ['required', 'string'],
            'route' => ['required', 'string'],
            'note' => ['sometimes', 'string'],
            'timetable_id' => ['required', 'integer', 'exists:App\Models\Timetable,id']
        ]);
    }
}
