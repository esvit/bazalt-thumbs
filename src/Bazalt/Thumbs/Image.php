<?php

namespace Bazalt\Thumbs;

require_once __DIR__ . '/helpers/thumb.php';

class Image
{
    const CONFIGURATION_FILE_EXTENSION = '.pre';

    protected static $staticFolder = null;

    protected static $staticUrl = null;

    protected static $rootDir = null;

    public static function initStorage($staticFolder, $staticUrl, $rootDir = '')
    {
        self::$staticFolder = $staticFolder;
        self::$staticUrl = $staticUrl;
        self::$rootDir = $rootDir;
    }

    public static function getThumb($image, $size, $options = [])
    {
        $image = self::$rootDir . $image;
        // check image
        if (!is_file($image) || !is_readable($image)) {
            throw new \Exception(sprintf('Invalid file "%s"', $image));
        }

        // check size
        list($width, $height) = explode('x', $size);
        if (!is_numeric($width) || !is_numeric($height)) {
            throw new \Exception(sprintf('Invalid size "%s"', $size));
        }
        $options = array_merge([
            'image' => $image,
            'size' => [
                'width' => $width,
                'height' => $height
            ]
        ], $options);

        $serializeOptions = serialize($options);
        $key = md5($serializeOptions);

        // save options in configuration file
        $staticFile = self::_getFilename($image, $key);
        if (!is_file($staticFile)) {
            file_put_contents($staticFile . self::CONFIGURATION_FILE_EXTENSION, $serializeOptions);
        }
        return sprintf(self::$staticUrl, $key{0}) . self::_relativePath($staticFile);
    }

    /**
     * Generate thumbnail by configuration file
     */
    public static function generateThumb($file, Operations $operations)
    {
        // antihacker
        if (substr_count($file, '../') != false) {
            return false;
        }
        if (is_file($file)) {
            return $file;
        }

        $preFile = $file . self::CONFIGURATION_FILE_EXTENSION;
        // check configuration file
        if (!is_file($preFile) || !is_readable($preFile)) {
            return false;
        }

        // read configuration
        $config = unserialize(file_get_contents($preFile));
        $imageFile = $config['image'];

        // check image file
        if (!is_file($imageFile) || !is_readable($imageFile)) {
            return false;
        }
        $imagine = new \Imagine\Gd\Imagine();
        // catch exception if invalid image
        try {
            $image = $imagine->open($imageFile);
        } catch (\Imagine\Exception\InvalidArgumentException $e) {
            return false;
        }
        $operations->originalImage($image);
        // do operations with image
        foreach ($config as $operation => $options) {
            if (method_exists($operations, $operation)) {
                $image = $operations->$operation($image, $options, $config);
            }
        }
        $image->save($file);
        return $file;
    }

    /**
     * Generate filename with folders
     */
    private static function _getFilename($file, $fileKey)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $folder = self::$staticFolder;

        $path  = rtrim($folder, DIRECTORY_SEPARATOR)  . DIRECTORY_SEPARATOR;
        $path .= $fileKey{0} . $fileKey{1} . DIRECTORY_SEPARATOR;
        $path .= $fileKey{2} . $fileKey{3} . DIRECTORY_SEPARATOR;

        if (!is_dir($path) && !mkdir($path, 0777, true)) {
            throw new \Exception('Cant create folder "' . $path . '"');
        }
        return $path . $fileKey . '.' . strToLower($ext);
    }

    private static function _relativePath($path)
    {
        $siteDir = str_replace('\\', '/', self::$staticFolder);
        $path = str_replace('\\', '/', $path);

        $path = str_replace($siteDir, '', $path);
        return $path;
    }
}