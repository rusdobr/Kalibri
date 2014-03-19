<?php

namespace Kalibri {

    /**
     *  @package Kalibri
     *
     *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
     */
	class Db
	{
		/**
		 * Connected drivers list
		 * @var arrat
		 */
		protected $_connections = array();

		/**
		 * Default connection name that will be used
		 * @var string
		 */
		protected $_defaultConnectName = '';

		/**
		 * Stored config to reduce config values requests
		 * @var array
		 */
		protected $_config = array();

		/**
		 * Executed queries list
		 * @var array
		 */
		protected $_queries = array();
		protected $_useCache = false;

//------------------------------------------------------------------------------------------------//
		public function __construct()
		{
			$this->_config = \Kalibri::config()->get('db');
			$this->_defaultConnectName = $this->_config['default'];

			$this->_useCache = \Kalibri::config()->get('cache.is-enabled');
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get connection to database
		 *
		 * @param string $name Connection name
		 * @param array $config Connection config (not required)
		 *
		 * @return \Kalibri\Db\Driver\Base
		 */
		public function getConnection( $name = null, $config = null )
		{
			// Use default connection name if name is not passed
			$name = $name ?: $this->_defaultConnectName;

			// Check is driver already connected
			if( $this->isConnected( $name ) )
			{
				return $this->_connections[ $name ];
			}

			// Is config not passed and we have stored config
			if( !$config && isset( $this->_config['connection'][ $name ] ))
			{
				$config = $this->_config['connection'][ $name ];
			}
			else
			{
				// Config not passed and not available in config file
				\Kalibri::error()->show("Invalid DB connection name '$name'.");
			}

			// Calculate driver name
			$driverName = '\Kalibri\Db\Driver\\'.\ucfirst( $config['driver'] );

			try
			{
				$this->_connections[ $name ] = new $driverName( $config );
				\Kalibri::logger()->add(\Kalibri\Logger\Base::L_INFO,
						"Connected to database: driverName=$driverName, connection=$name");
			}
			catch( Exception $e )
			{
				\Kalibri::error()->showException( $e );
			}

			// Return connected driver
			return $this->_connections[ $name ];
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Disconnect driver from data source
		 * 
		 * @param string $name Connection name
		 * 
		 * @return null
		 */
		public function disconnect( $name = null )
		{
			// Use default connection if not passed
			$name = $name ?: $this->_defaultConnectName;

			// Disconnect only if connected
			if( $this->isConnected( $name ) )
			{
				$this->_connections[ $name ]->disconnect();
			}
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Disconnect all connected drivers
		 * 
		 * @return null
		 */
		public function disconnectAll()
		{
			foreach( $this->_connections as $name=>$connect )
			{
				if( $connect instanceof \Kalibri\Db\Driver\Base
					&& $this->_connections[ $name ]->isConnected() )
				{
					$this->_connections[ $name ]->disconnect();
				}
			}
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Create instance of Query bulder
		 * 
		 * @return Kalibri\Db\Query
		 */
		public function getQuery()
		{
			return new \Kalibri\Db\Query();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Check is driver connected
		 *
		 * @param string $name Connection name
		 *
		 * @return bool
		 */
		public function isConnected( $name = null )
		{
			// Use default name if name not passed
			$name = $name ?: $this->_defaultConnectName;

			return isset( $this->_connections[ $name ] )
				&& $this->_connections[ $name ] instanceof \Kalibri\Db\Driver\Base
				&& $this->_connections[ $name ]->isConnected();
		}
	}
}