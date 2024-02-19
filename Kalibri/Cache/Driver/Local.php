<?php

namespace Kalibri\Cache\Driver;

class Local implements BaseInterface
{
    public function __construct( array $config = null ){}

    #[\Override]
    public function clear() {}
    #[\Override]
    public function get( $key ) {}
    #[\Override]
    public function remove( $key ){}
    #[\Override]
    public function set( $key, $value, $expire = 0 ) {}
}
