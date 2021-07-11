<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;
use InvalidArgumentException;

class ImageHelper
{
    /**
     * Takes an image file, saves it to storage and creates a thumbnail with specified dimensions.
     *
     * @param $file image
     * @param int $width width
     * @param int $height height
     * @return bool
     */
    public static function storeImageAndCreateThumbnail($file, int $width, int $height): bool
    {
        //Save image to storage
        $fileName = ImageHelper::generateFilename($file);
        Storage::putFileAs('images', $file, $fileName);

        //Create thumbnail
        ImageHelper::createThumbnailFromImage($file, $width, $height, $fileName);

        //Return whether images are saved.
        return Storage::disk('local')->exists('images/' . $fileName) && Storage::disk('local')->exists('images/thumbnails/' . $fileName);
    }

    /**
     * Returns string with date and time stamp for filename.
     *
     * @param $file
     * @return string
     */
    public static function generateFilename($file)
    {
        return date("Ymd-His") . "-" . $file->getClientOriginalName();
    }

    /**
     * Take an image file, create a thumbnail with desired size and save it.
     *
     * @param $file
     * @param int $width
     * @param int $height
     * @param string $fileName
     */
    public static function createThumbnailFromImage($file, int $width, int $height, string $fileName): void
    {
        $image = ImageManagerStatic::make($file);
        $type = $image->mime();

        //Resize
        if ($image->width() > $image->height()) {
            ImageHelper::resizeToHeight($image, $height);
        } else {
            ImageHelper::resizeToWidth($image, $width);
        }

        //Crop
        ImageHelper::cropImageToCenter($image, $width, $height);
        //Save
        ImageHelper::saveImageToStorage($image, $fileName, $type);
    }

    /**
     * Resize a given Intervention Image to specified heigh while conserving aspect ratio.
     *
     * @param \Intervention\Image\Image $image
     * @param int $newHeight
     * @return \Intervention\Image\Image
     */
    public static function resizeToHeight(\Intervention\Image\Image $image, int $newHeight)
    {
        $image->resize(null, $newHeight, function ($constraint) {
            $constraint->aspectRatio();
        });

        return $image;
    }

    /**
     * Resize a given Intervention Image to specified width while conserving aspect ratio.
     *
     * @param \Intervention\Image\Image $image
     * @param int $newWidth
     * @return \Intervention\Image\Image
     */
    public static function resizeToWidth(\Intervention\Image\Image $image, int $newWidth)
    {
        $image->resize($newWidth, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        return $image;
    }

    /**
     * Take an Intervention Image and crop it to the desired size with central alignment.
     *
     * @param \Intervention\Image\Image $image
     * @param int $cropWidth
     * @param int $cropHeight
     * @return \Intervention\Image\Image
     */
    public static function cropImageToCenter(\Intervention\Image\Image $image, int $cropWidth, int $cropHeight)
    {
        $image->crop($cropWidth, $cropHeight);

        return $image;
    }

    public static function saveImageToStorage(\Intervention\Image\Image $image, string $fileName, string $fileType)
    {
        switch ($fileType) {
            case 'image/jpeg':
                $file = $image->encode('jpg');
                Storage::put('images/thumbnails/' . $fileName, $file->__toString());
                break;
            case 'image/png':
                $file = $image->encode('png');
                Storage::put('images/thumbnails/' . $fileName, $file->__toString());
                break;
            case 'image/bmp':
                $file = $image->encode('bmp');
                Storage::put('images/thumbnails/' . $fileName, $file->__toString());
                break;
            case 'image/gif':
                $file = $image->encode('gif');
                Storage::put('images/thumbnails/' . $fileName, $file->__toString());
                break;
            case 'image/webp':
                $file = $image->encode('webp');
                Storage::put('images/thumbnails/' . $fileName, $file->__toString());
                break;
            default:
                throw new InvalidArgumentException("Filetype $fileType is not supported.");
        }
    }
}
