<?php

require '../vendor/autoload.php';

if (!isset($_GET['file'])) {
    if (isset($_GET['x']) && (int)$_GET['x'] > 0 &&
        isset($_GET['y']) && (int)$_GET['y'] > 0 &&
        isset($_GET['original'])
    ) {

        $sizesConfigFile = __DIR__ . '/sizes.json';
        $sizesConfig = null;
        $size = (int)$_GET['x'] . 'x' . (int)$_GET['y'];
        $image = __DIR__ . $_GET['original'];

        if (!defined('APPLICATION_ENV')) {
            define('APPLICATION_ENV', getenv('APPLICATION_ENV'));
        }
        define('DEVELOPMENT_STAGE', APPLICATION_ENV == 'development');
        define('PRODUCTION_STAGE',  APPLICATION_ENV == 'production');
        define('TESTING_STAGE',     APPLICATION_ENV == 'testing');

        \Bazalt\Thumbs\Image::initStorage(__DIR__, '');

        if (file_exists($sizesConfigFile)) {
            $sizesConfig = json_decode(file_get_contents($sizesConfigFile), true);
//            print_r($sizesConfig);exit;
        } else if (!DEVELOPMENT_STAGE) {
            header('HTTP/1.0 404 Not Found');
            exit('Not found');
        }
        if (DEVELOPMENT_STAGE) { //create pre file and push into sizes config
            if (!isset($sizesConfig[$size])) {
                $sizesConfig[$size] = [];
                if (!is_writable(__DIR__)) {
                    header('HTTP/1.0 500 Internal Server Error');
                    exit(__DIR__ . ' is not writable');
                }
                file_put_contents($sizesConfigFile, json_encode($sizesConfig, JSON_PRETTY_PRINT));
            }
            $_GET['file'] = \Bazalt\Thumbs\Image::getThumb($image, $size, $sizesConfig[$size]);
        } else if (isset($sizesConfig[$size])) { //check  sizes config
            $_GET['file'] = \Bazalt\Thumbs\Image::getThumb($image, $size);
        } else {
            header('HTTP/1.0 404 Not Found');
            exit('Not found');
        }
    } else {
        header('HTTP/1.0 404 Not Found');
        exit('Not found');
    }
}

class Operations extends \Bazalt\Thumbs\Operations
{
    public function watermark(\Imagine\Image\ImageInterface $image, $options, $allOptions)
    {
        $imagine = new \Imagine\Gd\Imagine();
        $wm = $imagine->open(__DIR__ . '/images/watermark.png');

        $size = $image->getSize();
        $wmSize = $wm->getSize();
        list($x, $y) = explode(' ', $options);
        if (!is_numeric($x)) {
            $x = ($x == 'right') ? ($size->getWidth() - $wmSize->getWidth()) : 0;
            if ($x < 0) $x = 0;
        }
        if (!is_numeric($y)) {
            $y = ($y == 'bottom') ? ($size->getHeight() - $wmSize->getHeight()) : 0;
            if ($y < 0) $y = 0;
        }

        $point = new \Imagine\Image\Point($x, $y);
        return $image->paste($wm, $point);
    }

    public function grayscale(\Imagine\Image\ImageInterface $image, $options, $allOptions)
    {
        $image->effects()->grayscale();
        return $image;
    }

    public function sepia(\Imagine\Image\ImageInterface $image, $options, $allOptions)
    {
        $image->effects()
            ->grayscale()
            ->colorize(new \Imagine\Image\Color('#643200'));
        return $image;
    }
}


$thumb = \Bazalt\Thumbs\Image::generateThumb(__DIR__ . $_GET['file'], new Operations());
if ($thumb) {
    switch (pathinfo($thumb, PATHINFO_EXTENSION)) {
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'jpg':
            header('Content-Type: image/jpeg');
            break;
    }
    readfile($thumb);
    exit;
}