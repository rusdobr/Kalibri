<?php

namespace Kalibri {

	class Form
	{
		const METHOD_POST = 'post';
		const METHOD_GET = 'get';
		const METHOD_ANY = 'any';

		/**
		 * Validation errors
		 * @var array
		 */
		protected $_errors = array();
		protected $_data = array();
		protected $_name;
		protected $_def;

//------------------------------------------------------------------------------------------------//
		public function __construct( $name = null )
		{
			if( $name )
			{
				$this->setName( $name );
			}
		}

//------------------------------------------------------------------------------------------------//
		public function isExists( $name )
		{
			return \file_exists( \Kalibri::app()->getLocation(). 'Form/'.$name.'.php' );
		}

//------------------------------------------------------------------------------------------------//
		public function setName( $name )
		{
			if( $this->isExists( $name ) )
			{
				$this->_name = $name;
				$this->_def = include( \Kalibri::app()->getLocation(). 'Form/'.$name.'.php' );

				return true;
			}

			return false;
		}

//------------------------------------------------------------------------------------------------//
		public function getName()
		{
			return $this->_name;
		}

//------------------------------------------------------------------------------------------------//
		public function getDefinition()
		{
			return $this->_def;
		}

//------------------------------------------------------------------------------------------------//
		public function bind( $data )
		{
			foreach( $this->_def['fields'] as $name=>&$info )
			{
				if( isset( $data[ $name ] ) )
				{
					$info['value'] = $data[ $name ];
				}
			}

			return $this;
		}

//------------------------------------------------------------------------------------------------//
		public function isValid()
		{
			return true;

			$this->_errors = array();

			foreach( $this->_def['rules'] as $rule )
			{
				if( \Kalibri\Helper\Validate::rule( $this->_def['fields'], $rule ) )
				{

				}
			}
		}

//------------------------------------------------------------------------------------------------//
		public function getErrors()
		{
			return $this->_errors;
		}
	}
}