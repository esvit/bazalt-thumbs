<?php

namespace Bazalt\Thumbs;

class Operations
{
    protected $originalImage = null;

    public function originalImage($image = null)
    {
        if ($image != null) {
            $this->originalImage = $image;
        }
        return $this->originalImage;
    }

    /**
     * Resize image
     */
    public function size(\Imagine\Image\ImageInterface $image, $options, $allOptions)
    {
        $mode = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;

        $width = (int)$options['width'];
        $height = (int)$options['height'];

        $size = $image->getSize();
        if (!$width) {
            $width = $size->getWidth() * (float)$height / $size->getHeight();
        }
        if (!$height) {
            $height = $size->getHeight() * (float)$width / $size->getWidth();
        }
        return $image->thumbnail(new \Imagine\Image\Box($width, $height), $mode);
    }
}