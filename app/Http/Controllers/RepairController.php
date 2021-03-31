<?php

namespace App\Http\Controllers;

use App\Http\Resources\RepairResource;
use App\Models\Repair;
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
        $repair = Repair::create($this->validateRequestData());

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
        $repair->update($this->validateRequestData());

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
            'start_date' => ['required', 'date'],
            'end_date' => ['sometimes', 'date']
        ]);
    }
}
