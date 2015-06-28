<?php

namespace Kalibri;

use \Kalibri\Helper\Highlight;
use \Kalibri\Logger\Base as Logger;

/**
 *  @package Kalibri
 *
 *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
 */
class Error
{
//------------------------------------------------------------------------------------------------//
	/**
	 * Show exception screen
	 *
	 * @param \Exception $exception
	 */
	public function showException( \Exception $exception )
	{
		@ob_end_clean();

		$msg = sprintf(
			"%s\nFile: %s\nLine: %d\nTrace:\n%s",
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			$exception->getTraceAsString()
		);

		\Kalibri::logger()->add( Logger::L_EXCEPTION, $msg);

		$viewName = \Kalibri::config()->get('error.view.exception');

		if( $viewName )
		{
			$view = new \Kalibri\View( $viewName );
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

		$viewName = \Kalibri::config()->get('error.view.500');

		if( $viewName )
		{
			(new \Kalibri\View( $viewName ))
				->assignArray([
					'title'   => $title,
					'message' => $message
				])
				->render();
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

		$viewName = \Kalibri::config()->get('error.view.404');
		if( $viewName )
		{
			(new \Kalibri\View( $viewName ))
				->assignArray(['pageUrl' => \Kalibri::uri()->getUri()])
				->render();
		}

		exit();
	}

//------------------------------------------------------------------------------------------------//
	/**
	 * Show access denied page
	 */
	public function show403()
	{
		@ob_end_clean();

		$viewName = \Kalibri::config()->get('error.view.403');

		if( $viewName )
		{
			if( \Kalibri::controller() instanceof \Kalibri\Controller\Page )
			{
				\Kalibri::controller()
					->page()
						->setViewName( $viewName )
						->render();
			}
			else
			{
				(new \Kalibri\View($viewName))
					->render();
			}
		}

		exit();
	}

    public function showAccessDenied()
    {
        $this->show403();
    }

    public function showPageNotFound()
    {
        $this->show404();
    }
}
