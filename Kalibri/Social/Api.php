<?php

namespace Kalibri\Social;

class Api
{
	public function getFriends(){}
	public function getFriendsInApp(){}
	public function getMyProfile(){}
	public function getNetworkName(){}
	public function getProfile( $userId ){}
	public function getUserId( $networkName = null ){}
	
	public function invite( $userId, $message, $image = null ){}
}