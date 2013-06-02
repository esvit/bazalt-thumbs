<?php

namespace Bazalt\Thumbs;

class Operations
{
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