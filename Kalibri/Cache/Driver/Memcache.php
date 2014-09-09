<?php

namespace Kalibri\Cache\Driver;

class Memcache implements BaseInterface
{
    const DEFAULT_PORT = 11211;
    const DEFAULT_HOST = 'localhost';

    /**
     * @var Memcache
     */
    protected $_memcache;

    /**
     * @var array
     */
    protected $_local = array();

//------------------------------------------------------------------------------------------------//
    public function __construct( array $config = null )
    {
        $config = $config ?: \Kalibri::config()->get('cache');

        $this->_memcache = new \Memcache();

        foreach( $config['servers'] as $server )
        {
            $this->_memcache->addserver(
                $server['host'],
                $server['port'],
                true,
                isset( $server['weight'] )? $server['weight']: 1
            );
        }
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @see \Kalibri\Cache\BaseInterface::clear();
     */
    public function clear()
    {
        return $this->_memcache->flush();
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @see \Kalibri\Cache\BaseInterface::get();
     */
    public function get( $key )
    {
        if( isset( $this->_local[ $key ] ) ) {
            return $this->_local[ $key ];
        }

        if( \Kalibri::config()->get('debug.log.is-enabled', false) )
        {
            \Kalibri::logger()->add(\Kalibri\Logger\Base::L_DEBUG, 'GET: '.$key, $this);
        }

        if( ( $result = $this->_memcache->get( $key ) ) !== false )
        {
            return $this->_local[ $key ] = $result;
        }

        return null;
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @see \Kalibri\Cache\BaseInterface::set();
     */
    public function set( $key, $value, $expire = 0 )
    {
        if( \Kalibri::config()->get('debug.log.is-enabled', false) )
        {
            \Kalibri::logger()->add(\Kalibri\Logger\Base::L_DEBUG, "SET: (ttl=$expire) $key=".var_export( $value, true ), $this);
        }

        if( !$this->_memcache->replace( $key, $value, MEMCACHE_COMPRESSED, $expire ) )
        {
            $this->_memcache->set( $key, $value, MEMCACHE_COMPRESSED, $expire );
        }

        $this->_local[ $key ] = $value;

        return $this;
    }

//------------------------------------------------------------------------------------------------//
    public function remove( $key )
    {
        if( \Kalibri::config()->get('debug.log.is-enabled', false) )
        {
            \Kalibri::logger()->add(\Kalibri\Logger\Base::L_DEBUG, 'REMOVE: '.$key, $this);
        }

        unset( $this->_local[ $key ] );
        return $this->_memcache->delete( $key );
    }
}
