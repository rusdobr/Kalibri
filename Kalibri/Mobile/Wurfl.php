<?php

namespace Kalibri\Mobile {

	include dirname( __FILE__).'/../../WURFL/wurfl_config.php';

	class Wurfl
	{
		/**
		 * @var WURFL_WURFLManager
		 */
		protected $instance;

		public function __construct()
		{
			// Create WURFL Configuration
			$wurflConfig = new \WURFL_Configuration_InMemoryConfig();

			// Set location of the WURFL File
			$wurflConfig->wurflFile(WURFL_RESOURCES.'/wurfl.xml');

			// Set the match mode for the API ('performance' or 'accuracy')
			$wurflConfig->matchMode('performance');

			// Automatically reload the WURFL data if it changes
			$wurflConfig->allowReload(true);

			// Setup WURFL Persistence
			$wurflConfig->persistence('file', array('dir' => WURFL_RESOURCES.'/storage/persistence'));

			// Setup Caching
			$wurflConfig->cache('file', array('dir' => WURFL_RESOURCES.'/storage/cache', 'expiration' => 36000));

			// Create a WURFL Manager Factory from the WURFL Configuration
			$wurflManagerFactory = new \WURFL_WURFLManagerFactory($wurflConfig);

			// Create a WURFL Manager
			$this->instance = $wurflManagerFactory->create();
		}

		/**
		 * @return WURFL_WURFLManager
		 */
		public function getInstance()
		{
			return $this->instance;
		}
	}
}