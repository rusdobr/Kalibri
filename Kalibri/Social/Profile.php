<?php

namespace Kalibri\Social;

class Profile
{
	public $id;
	public $firstName;
	public $lastName;
	public $name;
	public $email;
	public $gender;
	public $birthday;
	public $isInstalled;
	public $pictureLarge;
	public $pictureSmall;
	public $pictureNormal;
	
	public function __construct( array $data = null )
	{
		$this->setData( $data );
	}
	
	public function setData( array $data )
	{
		if( is_array( $data ) )
		{
			foreach( $data as $key=>$value )
			{
				$this->$key = $value;
			}
		}
	}
}