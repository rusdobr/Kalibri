<?php

namespace Kalibri\Social\Api;

class OAuth
{
	protected $_userId;
	protected $_networkName;
	protected $_config;
	protected $_accessToken;
	protected $_users = array();
	
//------------------------------------------------------------------------------------------------//
	public function __construct( array $config, $networkName = null )
	{
		$this->_config = $config;
		
		$this->_accessToken = isset( $_SESSION[ $this->_networkName.'access_token' ] )
			? $_SESSION[ $this->_networkName.'access_token' ]
			: null;
		
		$this->_userId = isset( $_SESSION[ $this->_networkName.'user_id' ] )
			? $_SESSION[ $this->_networkName.'user_id' ]
			: null;
		
		$this->_networkName = $networkName?: $this->_networkName;
	}

//------------------------------------------------------------------------------------------------//
	public function authorize( $code )
	{
		$result = $this->_call(
			'access_token', 
			array(
				'client_id'=>$this->_config['app-id'], 
				'client_secret'=>$this->_config['secret'], 
				'code'=>$code,
				'redirect_uri'=>\Url::site()
			), 
			$this->config['gw']['auth'] 
		);
		
		if( isset( $result['access_token'] ) )
		{
			$this->_accessToken = $result['access_token'];
			$this->_userId = isset( $result['user_id'])? $result['user_id']: null;
			
			if( isset( $result['user_id'] ) )
			{
				$this->_userId = $result['user_id'];
			}
			else
			{
				$user = $this->getProfile();
				$this->_userId = $user['id'];
			}
			
			$_SESSION[$this->_networkName.'access_token'] = $this->_accessToken;
			$_SESSION[$this->_networkName.'user_id'] = $this->_userId;
			
			return true;
		}
		
		return false;
	}
	
//------------------------------------------------------------------------------------------------//
	public function getNetworkName()
	{
		return $this->_networkName;
	}
	
//------------------------------------------------------------------------------------------------//
	public function getUserId()
	{
		return $this->_userId;
	}
	
//------------------------------------------------------------------------------------------------//
	protected function _call( $action, array $params = null, $apiGw = null )
	{
		$url = ($apiGw?: $this->_config['gw']['api']).$action.'?';
		
		if( $params !== null )
		{
			foreach( $params as $key=>$value )
			{
				$url .= $key.'='.urlencode( $value ).'&';
			}
		}
		
		if( $this->_accessToken )
		{
			$url .= 'access_token='.$this->accessToken;
		}

		$result = file_get_contents( $url );
		
		if( ($jsonResult = json_decode( $result, true )) )
		{
			$result = $jsonResult;
		}
		elseif( strpos( $result, '&') !== false && strpos( $result, '=') !== false )
		{
			parse_str( $result, $result );
		}
		
		return isset( $result['response'] )? $result['response']: $result;
	}
}