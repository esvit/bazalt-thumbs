<?php

namespace Bazalt\Thumbs;

class Extension extends \Twig_Extension
{
    public function getName()
    {
        return 'bazalt_thumbs';
    }

    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'thumb' => new \Twig_Filter_Function('thumb')
        );
    }
}