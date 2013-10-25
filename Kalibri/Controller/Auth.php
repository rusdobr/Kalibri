<?php

namespace Kalibri\Controller {

	class Auth extends Page
	{
		public function __construct()
		{
			$this->autoFindView( true );
		}

		public function login()
		{	
			if( \Kalibri::auth()->getProfile() )
			{
				\Url::redirect( \Kalibri::config()->get('page.after-login') );
			}

			if( isset( $_POST['login'], $_POST['password'] ) )
			{			
				if( \Kalibri::auth()->tryLogin( $_POST['login'], $_POST['password'] ) )
				{
					\Url::redirect( \Kalibri::config()->get('page.after-login') );
				}
				else
				{
					$this->page()->errorMsg = tr('Login failed');
				}
			}

			$this->page()->setTitle( tr('Sign in') );
		}

		public function logout()
		{
			$this->autoFindView( false );
			\Kalibri::auth()->logout();
			\Url::redirect( \Kalibri::config()->get('page.home', '/') );
		}

		public function register()
		{
			if( \Kalibri::auth()->getProfile() )
			{
				\Url::redirect( \Kalibri::config()->get('page.after-login') );
			}

			$this->page()->setTitle( tr('Sign up') );
			if( isset( $_POST['login'], $_POST['password'], $_POST['re-password'] ) )
			{
				$errors = array();

				if( empty( $_POST['password'] ) )
				{
					$errors[] = tr('Password should not be empty.');
				}

				if( $_POST['password'] !== $_POST['re-password'] )
				{
					$errors[] = tr('Password and Re-password should match.');
				}

				if( strlen( $_POST['password'] ) < \Kalibri::config()->get('auth.min-password-length') )
				{
					$errors[] = tr('Password should be minimum :min-length letters.', array(
						'min-length'=>\Kalibri::config()->get('auth.min-password-length')
					));
				}

				if( empty( $_POST['login'] ) )
				{
					$errors[] = tr('Login should not be empty.');
				}

				if( !\Kalibri::auth()->isValidLogin( $_POST['login'] ) )
				{
					$errors[] = tr('Login should contain latin letters or digits and be from 4 to 15 chars long.');
				}

				if( \Kalibri::auth()->getProfileByLogin( $_POST['login'] ) )
				{
					$errors[] = tr('Profile with this name already registered.');
				}

				$this->page()->errorMsg = $errors;

				if( !count( $errors ) )
				{
					\Kalibri::model('user')->register( array(
						'login'=>$_POST['login'],
						'password'=>\Kalibri::auth()->encryptPassword( $_POST['password'] )
					));

					if( \Kalibri::auth()->tryLogin($_POST['login'], $_POST['password']) )
					{
						\Url::redirect( \Kalibri::config()->get('page.after-login') );
					}
					else
					{
						\Kalibri::error()->show( tr('Ooops. Something go wrong.'));
					}
				}
			}
		}
	}
}