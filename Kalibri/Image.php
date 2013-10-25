<?php

namespace Kalibri;

class Image
{
	protected $_handler;
	protected $_fileName;
	
	public function __construct( $fileName )
	{
		if( $fileName )
		{
			$this->setFileName( $fileName );
		}
	}
	
	public function setFileName( $fileName )
	{
		if( \file_exists( $fileName ) )
		{
			$this->_fileName = $fileName;
			return $this;
		}
		
		throw new \Kalibri\Exception('Image file not found');
	}
}