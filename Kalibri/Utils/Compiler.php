<?php

namespace Kalibri\Utils {

	class Compiler
	{
		const BASE_FILE_NAME = 'base';
		const CACHE_FOLDER = 'Data/Cache/';

		const NAME_BASE = 'base';
		const NAME_ROUTE = 'route';

		protected $_routeName;
		protected $_skipped = array();

//------------------------------------------------------------------------------------------------//
		public function getLocation( $name = self::NAME_ROUTE )
		{
			if( $name == self::NAME_ROUTE )
			{
				$name = $this->getRouteName();
			}

			return K_APP_FOLDER.self::CACHE_FOLDER.$name.'.php';
		}

//------------------------------------------------------------------------------------------------//
		public function getRouteName()
		{
			if( !$this->_routeName )
			{
				$this->_routeName = \Kalibri::router()->getController().'_'.\Kalibri::router()->getAction();
			}

			return $this->_routeName;
		}

//------------------------------------------------------------------------------------------------//
		public function skip( $file )
		{
			$this->_skipped[] = $file;
		}

//------------------------------------------------------------------------------------------------//
		public function includeCached( $name )
		{
			$location = $this->getLocation( $name );
			$this->skip( $location );

			return include_once( $location );
		}

//------------------------------------------------------------------------------------------------//
		public function compile( $name = null )
		{
			$name = $name ?: self::BASE_FILE_NAME;

			if( !( $isCompiledExists = file_exists( $location = $this->getLocation( $name ) ) ) )
			{
				$includedeFiles = get_included_files();

				// Remove first 4 files (index, _init, Kalibri, Compiler )
				$includedeFiles = array_slice( $includedeFiles, 4 );

				foreach( $includedeFiles as $file )
				{
					if( in_array( $file, $this->_skipped ) || strpos( $file, '/Helper/' ) !== false || strpos( $file, '/Config/' ) !== false )
					{
						continue;
					}

					if( $name == self::NAME_BASE 
						|| ( $name == self::NAME_ROUTE && strpos($file, '_compiled.php') === false ) )
					{
						if( !$isCompiledExists )
						{
							file_put_contents( $location, '<?php', FILE_APPEND);
							chmod($location, 0777);
						}
						$this->skip( $file );

						// Drop comments, new lines and put content into "compiled" file
						exec("cat $file | php -w | sed 's/<?php//g' >> $location", $output);
						//  file_put_contents( $location, str_replace( array('<?php', '<?', '? >'), '', file_get_contents( $file ) ), FILE_APPEND);

						$isCompiledExists = true;
					}
				}

				//file_put_contents( $this->getLocation( $name, true ), json_encode($this->_infos[ $name ]) );
			}
		}
	}
}