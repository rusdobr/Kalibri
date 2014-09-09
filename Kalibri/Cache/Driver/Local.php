<?php

namespace Kalibri\Cache\Driver;

class Local implements BaseInterface
{
    public function __construct( array $config = null ){}

    public function clear() {}
    public function get( $key ) {}
    public function remove( $key ){}
    public function set( $key, $value, $expire = 0 ) {}
}
