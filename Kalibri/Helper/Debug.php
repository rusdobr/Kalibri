<?php
namespace Kalibri\Helper {

	/**
	 * @package Kalibri
	 * @subpackage Helpers
	 */
	class Debug implements \Kalibri\Helper\BaseInterface
	{
		public static function init( array $options = null ){}

//------------------------------------------------------------------------------------------------//
		public static function getPanel()
		{
			$view = new \Kalibri\View('Debug/panel');
			$view->marks = \Kalibri::benchmark()->getMarks( true );

			return $view->render( true );
		}
	}
}