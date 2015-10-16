<?php

namespace Kalibri\Utils\Html
{
	class TagNode extends Node
	{
		protected $name;
		protected $classes = array();

		public function getName()
		{
			return $this->name;
		}

        public function setName($name) {
            $this->name = $name;
            return $this;
        }

		public function getText()
		{
			if( $this->childs )
			{
				$text = '';

				foreach( $this->childs as $child )
				{
					$text .= $child->getText();
				}

				return $text;
			}
		}

        public function toHtml()
        {
            $result = '<'.$this->getName();

            foreach( $this->attributes as $name=>$value )
            {
                if( is_array( $value ) && count( $value ) )
                {
                    if( $name == 'style' )
                    {
                        $result .= ' style="'.implode(';', $value).'"';
                        continue;
                    }
                    elseif( $name == 'class')
                    {
                        $result .= ' class="'.implode(' ', $value).'"';
                        continue;
                    }
                }

                $result .= " $name=\"$value\"";
            }

            if( !$this->isSelfClosing )
            {
                $result .= '>';
            }

            foreach( $this->childs as $child )
            {
                $result .= $child->toHtml();
            }

            return $result . ($this->isSelfClosing? ' />': "</{$this->name}>");
        }

        public function setClasses(array $classes)
        {
            $this->classes = $classes;
            $this->attributes['class'] = implode(' ', $classes);

            return $this;
        }

		public function hasClass( $class )
		{
            if( is_array( $class ) )
            {
                foreach( $class as $value )
                {
                    if( $this->hasClass($value) )
                    {
                        return true;
                    }
                }
            }
            else
            {
			    return in_array( $class, $this->classes );
            }

            return false;
		}

		public function getChilds()
		{
			return $this->childs;
		}

		public function findBySelector( array $path, $mode = null )
		{
			if( !count( $path ) )
			{
				return array();
			}

			$fullPath = $path;
			$step = array_shift( $path );

			if( in_array( $step, array(' ', '>', '+') ) )
			{
				$mode = $step;
				$step = array_shift( $path );
			}

            $current = array();
            $pathLength = count( $fullPath );
            $conditions = Node::prepareConditions( $step );

			if( $this->isConditionsSatisfied( $conditions ) )
			{
				if( $pathLength == 1 )
				{
					$current[] = $this;
				}
				else
				{
					$current = array_merge( $current, $this->findBySelector( $path, $mode ) );
				}
			}

			foreach( $this->childs as $child )
			{
				if( $child instanceof TagNode )
				{
					if( $child->isConditionsSatisfied( $conditions ) )
					{
						if( $pathLength == 1 )
						{
							$current[] = $child;
						}
						else
						{
							$current = array_merge( $current, $child->findBySelector( $path, $mode ) );
						}
					}
					else
					{
						$current = array_merge( $current, $child->findBySelector( $fullPath, $mode ) );
					}
				}
			}

			return $current;
		}

		public function find( $selector )
		{
			$selectors = explode(',', trim( $selector) );
			$current = array();

			foreach( $selectors as $strPath )
			{
				$current = array_merge( $current, $this->findBySelector( explode(' ', trim( $strPath ) ) ) );
			}

			return $current;
		}

		public function process( Document &$document )
		{
			$tmp = null;
			$position = 0;
			$isAttributeValue = false;
			$lastAttribute = null;

			while( $this->raw[ $position ] != '>' )
			{
				$position++;

				if( ( $this->raw[ $position ] == ' ' && !$isAttributeValue ) || $this->raw[ $position ] == '>'
					|| ( !$isAttributeValue && $this->raw[ $position ] == '/' ) )
				{
					if( !$tmp )
					{
						continue;
					}

					if( !$this->name )
					{
						$this->name = $tmp;
						$tmp = null;
					}
				}
				elseif( $this->raw[ $position ] == '=' && !$isAttributeValue )
				{
					$lastAttribute = $tmp;
					$tmp = null;
				}
				elseif( $this->raw[ $position ] == '"' || $this->raw[ $position ] == "'" )
				{
					if( $isAttributeValue )
					{
						if( !is_array( $this->attributes ) )
						{
							$this->attributes = array();
						}

						$this->attributes[ $lastAttribute ] = $tmp;
						$tmp = null;
						$isAttributeValue = $lastAttribute =false;
					}
					else
					{
						$isAttributeValue = true;
					}
				}
				else
				{
					$tmp .= $this->raw[ $position ];
				}
			}

			$endsWith = $this->raw[ $position -1 ].$this->raw[ $position ];
			$this->raw = substr( $this->raw, $position+1 );

			if( count( $this->attributes ) )
			{
				if( isset( $this->attributes['id'] ) )
				{
					$document->registerId( $this->attributes['id'], $this );
				}

				if( isset( $this->attributes['class'] ) )
				{
					$this->classes = explode( ' ', $this->attributes['class'] );
					$document->registerClasses( $this->classes, $this );
				}
			}

			// is tag closed
			if( $endsWith == '/>' || $document->isSelfClosing( $this->name ) )
			{
				return;
			}

			$this->processChilds( $position, $document );

            unset( $this->raw );
		}

		protected function processChilds( $position, Document &$document )
		{
			// limit childs count to 10000
			for( $i=0; $i<10000; $i++ )
			{
				//Skip white spaces between tags
				if( $this->raw && $this->raw[0] == ' ' )
				{
					$this->raw = ltrim( $this->raw );
				}

				if( !$this->raw )
				{
					return;
				}

				// is next tag closed, just skip it
				if( $this->raw[0] == '<' && $this->raw[1] == '/')
				{
					$this->raw = substr( $this->raw, strpos( $this->raw, '>' )+1 );
					return;
				}

				if( !is_array( $this->childs ) )
				{
					$this->childs = array();
				}

				// Look for childs
				if( $this->raw[0] == '<' )
				{
					$this->childs[] = new TagNode( $this->raw, $document, $this );
				}
				else
				{
					$this->childs[] = new TextNode( $this->raw, $document, $this );
				}
			}
		}

		public function dump()
		{
			$str = '<li><strong>'.$this->name.'</strong>'.
				( isset( $this->attributes['id'] )? '#'.$this->attributes['id']:'' ).
				( isset( $this->attributes['class'] )? '.'.$this->attributes['class']:'' );

			if( $this->childs && count( $this->childs ) )
			{
				$str .= '<ul style="list-style:none;border-left:1px dashed gray;">';
				foreach( $this->childs as $child )
				{
					$str .= $child->dump();
				}
				$str .= '</ul>';
			}

			return $str.'</li>';
		}
	}
}