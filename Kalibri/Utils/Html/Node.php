<?php

namespace Kalibri\Utils\Html
{
	abstract class Node
	{
		protected $raw;
		/**
		 *	@var array
		 */
		protected $attributes = [];
        /**
         *	@var Node
         */
        protected $parent;
        /**
         *	@var array
         */
        protected $childs = [];
        /**
         * @var int
         */
        protected $inParentIndex;

        protected $isSelfClosing = false;

		public function __construct( &$raw, Document &$document, Node &$parent = null )
		{
			$this->raw = &$raw;
			//$this->document = &$document;
			$this->parent = &$parent;

            if( $this->parent )
            {
                $this->inParentIndex = count( $this->parent->getChilds() );
            }

			$this->process( $document );

            $this->isSelfClosing = $document->isSelfClosing( $this->getName() );
		}

        /**
         * @return Node
         */
        public function getNextSibling()
        {
            return $this->parent? $this->parent->getChild( $this->inParentIndex + 1 ): null;
        }

        /**
         * @return Node
         */
        public function getPrevSibling()
        {
            return $this->parent && $this->inParentIndex-1 >= 0
                ? $this->parent->getChild( $this->inParentIndex - 1 )
                : null;
        }

		public function getChild( $index )
		{
			return $this->childs[ $index ] ?? null;
		}

		public function getChilds()
		{
			return $this->childs;
		}

		public function getElementsByTagName( $name )
		{
			$list = [];
			
			if( $this instanceof TagNode )
			{
				if( $this->name == $name )
				{
					$list[] = $this;
				}

				if( is_array( $this->childs ) && count( $this->childs ) )
				{
					foreach( $this->childs as &$child )
					{
						if( $child instanceof TagNode && $child->getName() )
						{
							$list = array_merge( $list, $child->getElementsByTagName( $name ) );
						}
					}
				}
			}

			return $list;
		}

        /**
         * @return Node
         */
        public function getParent()
        {
            return $this->parent;
        }

		public function attr( $name, $value = false)
		{
            if( $value !== false ) {
                if( $value === null ) {
                    unset($this->attributes[$name]);
                } else {
                    $this->attributes[$name] = $value;
                }

                return $this;
            }

			return is_array( $this->attributes ) && isset( $this->attributes[ $name ] )
				? $this->attributes[ $name ]
				: null;
		}

        public function getAttributes()
        {
            return $this->attributes;
        }

        /**
         *  Transform selector step to conditions array with tag, class name
         *
         *  @param string $step Selector step (part of selector separated with space)
         *
         *  @return array
         */
        public static function prepareConditions( $step )
        {
            $match = [];
            $conditions = ['attr'=>[]];

            if( str_contains( $step, '[' ) && preg_match('/(.+)\[(.+)=["\'](.+)["\']\]/', $step, $match) )
            {
                $step = $match[1];
                $conditions['attr'][ $match[2] ] = $match[3];
            }

            // Tag with class or only classes chain
            if( str_contains( $step, '.' ) )
            {
                $parts = explode( '.', $step );
                $step = array_shift( $parts );

                $conditions['attr']['class'] = $parts;
            }

            // Tag with ID
            if( strpos( $step, '#' ) )
            {
                [$step, $conditions['attr']['id']] = explode( '#', $step );
            }

			if( $step && $step[0] == '#' )
            {
                $conditions['attr']['id'] = substr( $step, 1 );
            }
            elseif( $step && $step[0] == '.' )
            {
                if( isset( $conditions['attr']['class'] ) )
                {
                    $conditions['attr']['class'][] = substr( $step, 1 );
                }
                else
                {
                    $conditions['attr']['class'] = [substr( $step, 1 )];
                }
            }
            else
            {
                $conditions['tag'] = $step;
            }

            return $conditions;
        }

        public function isConditionsSatisfied( array $conditions )
        {
            $result = true;

            if( isset( $conditions['tag'] ) && $conditions['tag'] )
            {
                $result = $result && $this->getName() == $conditions['tag'];
            }

            if( $result && isset( $conditions['attr'] ) && is_array( $conditions['attr'] ) && count( $conditions['attr'] ) )
            {
                foreach( $conditions['attr'] as $name=>$value )
                {
                    if( $name == 'class' )
                    {
                        $result = $result && $this->hasClass( $value );
                    }
                    else
                    {
                        $result = $result && $this->attr($name) == $value;
                    }
                }
            }

            return $result;
        }

		abstract function dump();
		abstract function getText();
		abstract function process( Document &$document );
        abstract function getName();
        abstract function toHtml();
	}
}