<?php

namespace Kalibri {

	use \Kalibri\Helper\Highlight as Highlight;
	use \Kalibri\Logger\Base as Logger;

	/**
	 * @package Kalibri
	 */
	class Error
	{
//------------------------------------------------------------------------------------------------//
		/**
		 * Show ecxception screen
		 *
		 * @param \Exception $exception
		 */
		public function showException( \Exception $exception )
		{
			@ob_end_clean();
			\Kalibri::logger()->add( Logger::L_EXCEPTION, $exception->getMessage().' File: '.$exception->getFile().
					' Line: '.$exception->getLine()." Trace:\n".$exception->getTraceAsString());

			if( \Kalibri::config()->get('error.view.exception') )
			{
				$view = new \Kalibri\View( \Kalibri::config()->get('error.view.exception') );
				$view->ex = $exception;

				$str = '';
				$file = \fopen( $exception->getFile(), 'r' );
				for( $i = 0; $i < $exception->getLine()-16; $i++ )
				{
					\fgets( $file );
				}

				for( $i = 0; $i < 20; $i++ )
				{
					$str .= \fgets( $file );
				}

				$view->code = Highlight::php( $str, true, 1, $exception->getLine() );

				if( $view->isExists() )
				{
					$view->render();
				}
				else
				{
					// Fallback to show any message in case if exception view not found or not set
					echo "<h1>Exception</h1><p>{$exception->getMessage()}</p>";
				}
			}

			exit();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Show error screen
		 *
		 * @param string $title
		 * @param string $message
		 */
		public function show( $message, $title = 'Error' )
		{
			\Kalibri::event()->trigger('genericError');

			@ob_end_clean();
			\Kalibri::logger()->add( Logger::L_ERROR, $message);

			if( \Kalibri::config()->get('error.view.500') )
			{
				$view = new \Kalibri\View( \Kalibri::config()->get('error.view.500') );
				$view->title = $title;
				$view->message = $message;

				$view->render();
			}

			exit();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Show page not found page
		 */
		public function show404()
		{
			\Kalibri::event()->trigger('pageNotFoundError');

			@ob_end_clean();
			\Kalibri::logger()->add( Logger::L_ERROR, 'Page not found: '.\Kalibri::uri()->getUri() );

			if( \Kalibri::config()->get('error.view.404') )
			{
				$view = new \Kalibri\View( \Kalibri::config()->get('error.view.404') );
				$view->pageUrl = \Kalibri::uri()->getUri();

				$view->render();
			}

			exit();
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Show access denied page
		 */
		public function show403()
		{
			//\Kalibri::event()->triggerByName('accessDeniedError');

			@ob_end_clean();
			//\Kalibri::logger()->add( Logger::L_ERROR, 'Access denied: '.\Kalibri::uri()->getUri() );

			if( \Kalibri::config()->get('error.view.403') )
			{
				if( \Kalibri::controller() instanceof \Kalibri\Controller\Page )
				{
					\Kalibri::controller()->page()->setViewName( \Kalibri::config()->get('error.view.403') )->render();
				}
				else
				{
					$view = new \Kalibri\View( \Kalibri::config()->get('error.view.403') );
					$view->render();
				}
			}

			exit();
		}
	}
}