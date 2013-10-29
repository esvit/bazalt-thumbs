<?php

function thumb($image, $size, $options = array())
{
    return \Bazalt\Thumbs\Image::getThumb($image, $size, $options);
}