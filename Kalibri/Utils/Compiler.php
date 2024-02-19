<?php

namespace Kalibri\Utils {

	class Compiler
	{
		public const BASE_FILE_NAME = 'base';
		public const CACHE_FOLDER = 'Data/Cache/';

		public const NAME_BASE = 'base';
		public const NAME_ROUTE = 'route';

		protected $_routeName;
		protected $_skipped = [];

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
		public function skip( $file ): void
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
		public function compile( $name = null ): void
		{
			$name = $name ?: self::BASE_FILE_NAME;

			if( !( $isCompiledExists = file_exists( $location = $this->getLocation( $name ) ) ) )
			{
				$includedFiles = get_included_files();

				// Remove first 4 files (index, _init, Kalibri, Compiler )
				$includedFiles = array_slice( $includedFiles, 4 );

				foreach( $includedFiles as $file )
				{
					if( in_array( $file, $this->_skipped ) || str_contains( $file, '/Helper/' ) || str_contains( $file, '/Config/' ) )
					{
						continue;
					}

					if( $name == self::NAME_BASE 
						|| ( $name == self::NAME_ROUTE && !str_contains($file, '_compiled.php') ) )
					{
						if( !$isCompiledExists )
						{
							file_put_contents( $location, '<?php', FILE_APPEND);
							chmod($location, 0777);
						}
						$this->skip( $file );

						// Drop comments, new lines and put content into "compiled" file
						exec("cat $file | php -w | sed 's/<?php//g' >> $location");
						//  file_put_contents( $location, str_replace( array('<?php', '<?', '? >'), '', file_get_contents( $file ) ), FILE_APPEND);

						$isCompiledExists = true;
					}
				}

				//file_put_contents( $this->getLocation( $name, true ), json_encode($this->_infos[ $name ]) );
			}
		}
	}
}