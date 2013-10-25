<?php

namespace Kalibri\Social\Api;

class Vk extends OAuth implements \Kalibri\Social\Api\BaseInterface
{
	protected $_networkName = 'vk';

//------------------------------------------------------------------------------------------------//
	public function getFriends()
	{
		$list = array();
		$params = array(
			'fields'=>'uid,first_name,photo_rec,photo_medium_rec,photo_big,gender,bdate,sex',
			'uid'=>$this->_userId
		);
		
		$result = $this->_call('friends.get', $params);

		foreach( $result as $friend )
		{
			$list[ $friend['uid'] ] = $this->_createUser( $friend );
			$this->_users[ $friend['uid'] ] = $list[ $friend['uid'] ];
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
		
		$params = array(
			'fields'=>'uid,first_name,photo_rec,photo_medium_rec,photo_big,bdate,sex',
			'uids'=>$userId
		);
		
		$result = $this->_call( 'getProfiles', $params );
		
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
			'id'=>$data['uid'],
			'birthday'=>isset( $data['bdate'] )? $data['bdate']: null,
			'firstName'=>$data['first_name'],
			'lastName'=>$data['last_name'],
			'name'=>$data['first_name'].' '.$data['last_name'],
			'gender'=>isset( $data['sex'] )?( $data['sex'] == 1? 'male': ($data['sex'] == 2?'female':null)): null,
			'pictureLarge'=> isset( $data['photo_big'] )? $data['photo_big']: null ,
			'pictureNormal'=>isset( $data['photo_medium_rec'] )? $data['photo_medium_rec']: null,
			'pictureSmall'=> isset( $data['photo_rec'] )? $data['photo_rec']: null
		) );
	}
}