<?php

namespace Nearata\EncryptMail\Filesystem;

use Flarum\Foundation\Paths;
use Flarum\Http\UrlGenerator;

class EncryptMailDisk
{
    public function __invoke(Paths $paths, UrlGenerator $_)
    {
        return [
            'root' => "$paths->base/nearataEncryptMail",
            'url'  => null
        ];
    }
}
