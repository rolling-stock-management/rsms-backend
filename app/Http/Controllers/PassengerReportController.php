<?php

namespace App\Http\Controllers;

use App\Http\Requests\PassengerReportStoreRequest;
use App\Http\Requests\PassengerReportUpdateRequest;
use App\Http\Resources\PassengerReportResource;
use App\Models\PassengerReport;
use Symfony\Component\HttpFoundation\Response;

class PassengerReportController extends Controller
{
    /**
     * PassengerReportController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(PassengerReport::class, 'passenger_report');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $passengerReports = PassengerReport::paginate(10);

        return PassengerReportResource::collection($passengerReports);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(PassengerReportStoreRequest $request)
    {
        $passengerReport = PassengerReport::create($request->validated());

        return (new PassengerReportResource($passengerReport))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PassengerReport  $passengerReport
     * @return PassengerReportResource|\Illuminate\Http\Response
     */
    public function show(PassengerReport $passengerReport)
    {
        return new PassengerReportResource($passengerReport);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PassengerReport  $passengerReport
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(PassengerReportUpdateRequest $request, PassengerReport $passengerReport)
    {
        $passengerReport->update($request->validated());

        return (new PassengerReportResource($passengerReport))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PassengerReport  $passengerReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(PassengerReport $passengerReport)
    {
        $passengerReport->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }
}
