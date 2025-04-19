<?php

namespace zfx;

class Dynamic
{

    protected $path;

    public function __construct()
    {
        $this->path = Config::get('zafDynamicPath') . StrFilter::getID(uniqid('zaf_', TRUE), '') . DIRECTORY_SEPARATOR;
        @mkdir($this->path);
    }

    // --------------------------------------------------------------------

    public function getPath(): string
    {
        return $this->path;
    }

    // --------------------------------------------------------------------

    public function saveString($txt, $name)
    {
        file_put_contents($this->path . $name, $txt);
    }

    // --------------------------------------------------------------------

}
