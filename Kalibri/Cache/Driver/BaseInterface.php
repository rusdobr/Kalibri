<?php

namespace Kalibri\Cache\Driver;

interface BaseInterface
{
//------------------------------------------------------------------------------------------------//
	/**
	 * Construct cache driver
	 */
	public function __construct( array $config = null );
//------------------------------------------------------------------------------------------------//
	/**
	 * Get value from cache engine by string key
	 *
	 * @param string $key Key to find in cache server
	 *
	 * @throws \Kalibri\Cache\Exception with FAILED_TO_GET code
	 *
	 * @return mixed Stored value
	 */
	public function get( $key );
//------------------------------------------------------------------------------------------------//
	/**
	 * Store value in a cache engine.
	 *
	 * @param string $key Key to associate value
	 * @param mixed $value Value to store
	 * @param int $expire
	 *
	 * @return null
	 */
	public function set( $key, mixed $value, $expire = 0 );
//------------------------------------------------------------------------------------------------//
	/**
	 * Clear the whole storage
	 *
	 * @return null
	 */
	public function clear();
//------------------------------------------------------------------------------------------------//
	/**
	 * Remove single from storage by key
	 *
	 * @param string $key Key to remove
	 *
	 * @return null
	 */
	public function remove( $key );
}