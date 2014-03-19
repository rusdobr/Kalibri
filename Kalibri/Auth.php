<?php

namespace Kalibri {

    /**
     *  @package Kalibri
     *
     *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
     */
	class Auth
	{
		/**
		 * @var \Kalibri\Model\User
		 */
		protected $_mUser;
		protected $_myProfileId;

		/**
		 * Local profiles cache, this should prevent unnecessary hitting of memcache and db
		 * @var array
		 */
		protected $_profiles = array();

		protected $_config = array();

		protected $_passwordSalt;
		protected $_loginFailed;
		protected $_profileClass;


//------------------------------------------------------------------------------------------------//
		public function __construct()
		{
			$config = \Kalibri::config()->get('auth');

			$this->_passwordSalt = $config['salt'];
			$this->_loginFailed = isset( $config['login-field'] )
				? $config['login-field']
				: 'login';

			$this->_profileClass = isset( $config['profile'] )
				? $config['profile']
				: '\\Kalibri\\Auth\\Profile';

			if( isset( $_SESSION['user-id'] ) )
			{
				$this->_myProfileId = $_SESSION['user-id'];
				//$this->_profiles[ $this->_myProfileId ] = new $this->_profileClass( $_SESSION['user-data'] );
			}
		}

//------------------------------------------------------------------------------------------------//
		public function tryLogin( $login, $rawPassword )
		{
			if( $this->isValidLogin( $login ) )
			{
				$user = \Kalibri::model('user')->getByLogin( $login );
				$password = $this->encryptPassword( $rawPassword );

				if( $user && $user['password'] === $password )
				{
					$user['user_id'] = (int)$user['user_id'];
					$this->_profiles[ $user['user_id'] ] = new $this->_profileClass( $user );
					$this->_myProfileId = $user['user_id'];

					$_SESSION['user-id'] = $user['user_id'];

					return true;
				}
			}

			return false;
		}

//------------------------------------------------------------------------------------------------//
		public function authorizeById( $userId )
		{
			$this->logout();

			$user = \Kalibri::model('user')->get( $userId );

			if( $user )
			{
				$_SESSION['user-id'] = (int)$user['user_id'];
				$this->_myProfileId = $_SESSION['user-id'];

				return true;
			}

			return false;
		}

//------------------------------------------------------------------------------------------------//
		public function logout()
		{
			session_destroy();
			session_start();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @param int $userId
		 * 
		 * @return \Kalibri\Auth\Profile
		 */
		public function getProfile( $userId = null )
		{
			$userId = $userId === null? $this->_myProfileId: (int)$userId;

			if( $userId && !isset( $this->_profiles[ $userId ] ) )
			{
				$user = \Kalibri::model('user')->get( $userId );

				if( $user )
				{
					$this->_profiles[ $userId ] = new $this->_profileClass( $user );
				}
			}

			return isset( $this->_profiles[ $userId ] )? $this->_profiles[ $userId ]: null;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * @param string $login
		 * 
		 * @return \Kalibri\Auth\Profile
		 */
		public function getProfileByLogin( $login )
		{
			// Try to find already loaded users
			if( count( $this->_profiles ) )
			{
				foreach( $this->_profiles as &$profile )
				{
					if( $profile->login == $login )
					{
						return $profile;
					}
				}
			}

			$user = \Kalibri::model('user')->getByLogin( $login );

			if( $user )
			{
				return $this->_profiles[ $user['user_id'] ] = new $this->_profileClass( $user );
			}

			return null;
		}

//------------------------------------------------------------------------------------------------//
		public function encryptPassword( $rawPassword )
		{
			return md5( $rawPassword. $this->_passwordSalt );
		}

//------------------------------------------------------------------------------------------------//
		public function isValidLogin( $login )
		{
			return (bool)preg_match( '/^[\w\d\@_\.]{4,255}$/', $login );
		}
	}
}