<?php

namespace Kalibri {
	
	class ConsoleApplication extends Application 
	{
		public function run()
		{	
			$this->init();
		}

//------------------------------------------------------------------------------------------------//
		public function shutdown()
		{
			if( \Kalibri::config()->get('debug.log.is-enabled', false) )
			{
				\Kalibri::logger()->add(\Kalibri\Logger\Base::L_DEBUG, 'shutdown', $this);
				\Kalibri::logger()->write();
			}
			exit();
		}

//------------------------------------------------------------------------------------------------//
		protected function init()
		{
			\Kalibri::config()->load();

			if( $this->_mode )
			{
				\Kalibri::config()->load( $this->_mode );
			}

			// Set list of classes that will be auto inited on use
			\Kalibri::setAutoInitClasses( \Kalibri::config()->get('init.classes') );
			
			if( \Kalibri::config()->get('debug.log.is-enabled', false) )
			{
				\Kalibri::logger()->add(\Kalibri\Logger\Base::L_DEBUG, 'init', $this);
			}
		}
	}
}