<?php

namespace Kalibri\Social\Api;

interface BaseInterface
{
	public function __construct( array $config, $networkName = null );
	public function getUserId();
	public function getNetworkName();
	public function getFriends();
	public function getFriendsInApp();
	public function getProfile( $userId = null );
	public function invite( $userId, $message, $image = null );
}