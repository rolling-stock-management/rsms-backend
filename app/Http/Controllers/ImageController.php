<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Http\Requests\ImageStoreRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Models\PassengerWagon;
use App\Rules\ImageablesArrayRules;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * ImageController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Image::class, 'image');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|Response
     */
    public function index()
    {
        $images = Image::all();

        return ImageResource::collection($images);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ImageStoreRequest $request
     * @return \Illuminate\Http\JsonResponse|Response|object
     */
    public function store(ImageStoreRequest $request)
    {
        $data = $request->validated();
        $fileContents = $data['file'];

        $data = array_merge($data, [
            'file_name' => ImageHelper::generateFilename($fileContents),
            'user_id' => Auth::user()->id
        ]);

        $image = Image::create($data);
        ImageHelper::storeImageAndCreateThumbnail($fileContents, 500, 500);

        foreach ($data['imageables']['passenger'] as &$item) {
            $wagon = PassengerWagon::find($item);
            $image->passengerWagons()->save($wagon);
        }

        return (new ImageResource($image))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Image $image
     * @return ImageResource|Response
     */
    public function show(Image $image)
    {
        return new ImageResource($image);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Image $image
     * @return \Illuminate\Http\JsonResponse|Response|object
     */
    public function update(Request $request, Image $image)
    {
        $data = $this->validateRequest();

        $image->passengerWagons()->sync([]);
        foreach ($data['imageables']['passenger'] as &$item) {
            $wagon = PassengerWagon::find($item);
            $image->passengerWagons()->save($wagon);
        }

        $image->update($data);

        return (new ImageResource($image))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Image $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        Storage::delete('images/' . $image->file_name);
        $image->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Validate data from request.
     *
     * @return mixed
     */
    private function validateRequest()
    {
        return request()->validate([
            'title' => ['required', 'string'],
            'description' => ['sometimes', 'string'],
            'date' => ['sometimes', 'date'],
            'imageables' => ['required', new ImageablesArrayRules],
        ]);
    }
}
