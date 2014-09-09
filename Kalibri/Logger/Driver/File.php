<?php

namespace Kalibri\Logger\Driver {

	class File extends \Kalibri\Logger\Base
	{
		const DEFAULT_LOGS_PATH = '../../logs/';

//------------------------------------------------------------------------------------------------//
		public function init( array $options = null )
		{
			parent::init( $options );

			// Set default logs path if not set yet
			if( !isset( $this->_options['path'] ) )
			{
				$this->_options['path'] = self::DEFAULT_LOGS_PATH;
			}
		}

//------------------------------------------------------------------------------------------------//
		public function add( $level, $message, $class = null )
		{
			// Is this message should be stored
			if( !isset( $this->_excludedLevels[ $level ] ) )
			{
				// Is class instance passed
				if( $class !== null && \is_object( $class ) )
				{
					$class = \get_class( $class );
				}

				$this->_messages[] = array(
					'level'=>$level,
					'msg'=>$message,
					'class'=>$class,
					'date'=>\date( self::DEFAULT_DATE_FORMAT )
				);

				return true;
			}

			// Skip this message
			return false;
		}

//------------------------------------------------------------------------------------------------//
		public function write()
		{
			// Skip empty log saving
			if( !count( $this->_messages ) )
			{
				return false;
			}

			if( (!\file_exists( $this->_options['path'] ) || 
					 !\is_dir( $this->_options['path'] ) || !is_writable( $this->_options['path'] ) ) 
					&& !@mkdir( $this->_options['path'], 0777, true ) )
			{
				\Kalibri::error()->show("Can't create log file in '".$this->_options['path']."'" );
			}
			
			if( ($fResource = @\fopen( $this->_options['path'].'k'.\date('Y-m-d').'.log', 'a' )) !== null )
			{
				\fwrite( $fResource, $this->getAsString() );
				\fclose( $fResource );
			}

			return true;
		}
		
//------------------------------------------------------------------------------------------------//
		public function clear()
		{
			$this->_messages = array();
		}
	}
}