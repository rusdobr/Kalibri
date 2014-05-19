<?php

namespace Kalibri\Utils\Html
{
	class Document
	{
		protected $raw;
		protected $url;
		protected $doctype;
		protected $isFetched = false;
		protected $selfClosing = array(
			'br', 'meta', 'hr', 'img', 'input', 'link', 'base', 'embed', 'spacer'
		);
		protected $ids = array();
		protected $classes = array();

		/**
		* @var Node
		*/
		protected $root;

		public function __construct( $url = null )
		{
			$this->selfClosing = array_flip( $this->selfClosing );

			if( $url )
			{
				$this->setUrl( $url );
			}
		}

		public function setRaw( $string )
		{
			$this->raw = $string;
			$this->url = null;
			$this->isFetched = true;
			$this->process();

			return $this;
		}

		public function setUrl( $url )
		{
			$this->url = $url;
			$this->raw = null;
			$this->isFetched = false;
			$this->fetch();

			return $this;
		}

		public function fetch()
		{
			if( $this->isFetched )
			{
				return true;
			}

			if( !$this->url )
			{
				throw new \Exception("Url is empty");
			}

			$this->raw = file_get_contents( $this->url );
			$this->isFetched = true;

			$this->process();
		}

		public function process()
		{
			if( !$this->raw )
			{
				throw new \Exception('Empty data');
			}

			$this->raw = preg_replace( array(
				'/<!--.*?-->/', '/<script.*?>.*?<\/script>/', '/<style.*?>.*?<\/style>/'), 
				'', 
				str_replace( array("\t", "\n", "\r"), array(' '), trim( $this->raw ) ) 
			);

			if( $this->raw[0] == '<' && $this->raw[1] == '!' )
			{
				$this->doctype = substr( $this->raw, 0, strpos( $this->raw, '>')+1 );
				$this->raw = substr( $this->raw, strlen( $this->doctype ) );
			}

			// Read root tag
			$this->root = new TagNode( $this->raw, $this );
		}

		/**
		 *	@return Node
		 */
		public function &getRoot()
		{
			return $this->root;
		}

		public function getText()
		{
			return $this->root? $this->root->getText(): null;
		}

		public function isSelfClosing( $name )
		{
			return isset( $this->selfClosing[ $name ] );
		}

		public function registerId( $id, Node &$node )
		{
			$this->ids[ $id ] = &$node;
			return $this;
		}

		public function registerClasses( $classDef, Node &$node )
		{
			$classes = $classDef;
			
			if( !is_array( $classDef ) ) 
			{
				$classes = explode(' ', $classDef);
			}
			
			if( is_array( $classes ) && count( $classes ) )
			{
				foreach( $classes as $name )
				{
					if( !isset( $this->classes[ $name ] ) )
					{
						$this->classes[ $name ] = array();
					}

					$this->classes[ $name ][] = &$node;
				}
			}

			return $this;
		}

		/**
		 *	@return Node
		 */
		public function getElementById( $id )
		{
			return isset( $this->ids[ $id ] )? $this->ids[ $id ]: null;
		}

		public function getElementsByTagName( $name )
		{
			return $this->root? $this->root->getElementsByTagName( $name ): array();
		}

		/**
		 *  Find multiple DOM elements matching class name.
         *
         * @param string|array $class Class to find. If array passed, matching element should contain all classes in the array
         *
         * @return \Kalibri\Utils\Html\TagNode[]
         */
        public function getElementsByClass( $class )
		{
			if( is_array( $class ) )
            {
                $elements = array();
                $classToFind = count( $class )? current( $class ): null;

                // Single class not found, so we can't find element matching all classes in the list
                if( !$classToFind || !isset( $this->classes[ $name ] ) )
                {
                    return array();
                }

                foreach( $this->classes[ $classToFind ] as $candidat )
                {
                    // Match all other classes
                    if( $candidat->hasClass( $class ) )
                    {
                        $elements[] = $candidat;
                    }
                }

                return count( $elements )? $elements: array();
            }

            // Match single class
            return isset( $this->classes[ $class ] )? $this->classes[ $class ]: array();
		}

		public function getIds()
		{
			return array_keys( $this->ids );
		}

		public function getClasses()
		{
			return array_keys( $this->classes );
		}

		public function find( $selector )
		{
			$selectors = explode(',', trim( $selector) );
			$current = $found = $elements = array();
			
			foreach( $selectors as $strPath )
			{
				$path = explode(' ', trim( $strPath ) );
                $firstStep = array_shift( $path );
                $findChild = count( $path ) > 0;
                $conditions = Node::prepareConditions( $firstStep );
				$elements = array();

                if( isset( $conditions['attr']['id'] ) )
				{
					$elements = array( $this->getElementById( $conditions['attr']['id'] ));
				}
				elseif( isset( $conditions['attr']['class'] ) )
				{
					$elements = $this->getElementsByClass( $conditions['attr']['class'] );
				}
				elseif( isset( $conditions['tag'] ) )
				{
                    $elements = $this->getElementsByTagName( $conditions['tag'] );
				}

                foreach( $elements as $element )
                {
                    if( $element->isConditionsSatisfied( $conditions ) )
                    {
                        if( $findChild )
                        {
                            $found = $element->findBySelector( $path );

                            if( count( $found ) )
                            {
                                $current = array_merge( $current, $found );
                            }
                        }
                        else
                        {
                            $current[] = $element;
                        }
                    }
                }
			}

			return $current;
		}
		
		public function dump()
		{
			return $this->root? '<ul style="list-style:none;margin:0;padding:0">'.$this->root->dump().'</ul>': '';
		}
	}
}