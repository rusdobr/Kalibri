<?php

namespace Kalibri\Social\Api;

class Facebook extends OAuth implements \Kalibri\Social\Api\BaseInterface
{
	protected $_networkName = 'facebook';

//------------------------------------------------------------------------------------------------//
	public function getFriends()
	{
		$list = array();
		$result = $this->_call('me/friends', array('fields'=>'id,first_name,last_name,installed,gender'));
		
		foreach( $result['data'] as $item )
		{
			$list[ $item['id'] ] = $this->_createUser( $item );
			// Store user localy to prevent getting it twice
			$this->_users[ $item['id'] ] = $list[ $item['id'] ];
		}
		
		return $list;
	}

//------------------------------------------------------------------------------------------------//
	public function getProfile( $userId = null )
	{
		$userId = $userId?: $this->_userId;
		
		if( isset( $this->_users[ $userId ] ) )
		{
			return $this->_users[ $userId ];
		}
		
		$result = $this->_call( $userId == $this->_userId? 'me': $userId, array('fields'=>'id,first_name,last_name,gender,installed,birthday,email' ) );
		
		if( $result )
		{
			$result = $this->_createUser( $result );
			$this->_users[ $result->id ] = $result;
		}
		
		return $result;
	}

//------------------------------------------------------------------------------------------------//
	public function invite( $userId, $message, $image = null ){}
//------------------------------------------------------------------------------------------------//
	public function getFriendsInApp(){}
	
//------------------------------------------------------------------------------------------------//
	protected function _createUser( $data )
	{
		return new \Kalibri\Social\Profile( array(
			'id'=>$data['id'],
			'email'=>isset( $data['email'] )? $data['email']: null,
			'birthday'=>isset( $data['birthday'] )? $data['birthday']: null,
			'firstName'=>$data['first_name'],
			'lastName'=>$data['last_name'],
			'name'=>$data['first_name'].' '.$data['last_name'],
			'gender'=>isset( $data['gender'] )? $data['gender']: null,
			'isInstalled'=>isset( $data['installed'] )? $data['installed']: null,
			'pictureLarge'=>$this->config['gw']['_api'].$data['id'].'/picture&type=large',
			'pictureNormal'=>$this->config['gw']['_api'].$data['id'].'/picture&type=normal',
			'pictureSmall'=>$this->config['gw']['_api'].$data['id'].'/picture&type=small',
		) );
	}
}