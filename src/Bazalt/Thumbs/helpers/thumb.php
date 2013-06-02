<?php

function thumb($image, $size, $options = [])
{
    return \Bazalt\Thumbs\Image::getThumb($image, $size, $options);
}