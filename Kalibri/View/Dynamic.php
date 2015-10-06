<?php

namespace Kalibri\View;

use Kalibri\View;

class Dynamic extends View
{
    protected $_viewsDir = '/Data/cache/dynamic-views';
    protected $_path;

    public function __construct( $content = null )
    {
        $this->_name = md5((string)$content);
        $this->_path = \Kalibri::tmp('dynamic-views/');

        $this->storeContent($this->_path, (string)$content);
    }

    public function isExists($name = null, $path = null)
    {
        return true;
    }

    public function render( $asString = false, $path = null )
    {
        return parent::render($asString, $path ?: $this->path);
    }

    public function getLocation( $name = null, $path = null )
    {
        return $this->_path.$this->_name.'.php';
    }

    private function storeContent($path, $content)
    {
        if( !file_exists($path) )
        {
            mkdir($path, 0777, true);
        }

        if( !file_exists($path.$this->_name.'.php') )
        {
            file_put_contents($path.$this->_name.'.php', $content);
        }
    }
}