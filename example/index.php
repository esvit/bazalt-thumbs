<?php

require '../vendor/autoload.php';

\Bazalt\Thumbs\Image::initStorage(__DIR__ . '/static', '/thumb.php?file=/static');

echo '<img src="';
echo thumb(__DIR__ . '/images/Chrysanthemum.jpg', '200x100', [ "watermark" => "right top" ]);
echo '" />';

// Twig example
$loader = new \Twig_Loader_String();
$twig = new \Twig_Environment($loader, array(
    'debug' => true,
    'auto_reload' => true
));

$twig->addExtension(new \Bazalt\Thumbs\Extension());

$images = [
    __DIR__ . '/images/Chrysanthemum.jpg'
];

$template = '
    {% for image in images %}
        <img src="{{ image|thumb("200x200", { "watermark": "right bottom" }) }}" />
    {% endfor %}';

echo $twig->render($template, ['images' => $images]);