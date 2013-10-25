<?php

namespace Kalibri\Controller {

	class Json extends \Kalibri\Controller\Base
	{
		/**
		 * Response data to be send
		 * 
		 * @var array
		 */
		protected $response = array();

//------------------------------------------------------------------------------------------------//
		public function _render( $asString = false )
		{
			$this->_isRendered = true;
			$renderedResponse = json_encode( $this->response );

			if( !$asString )
			{
				echo $renderedResponse;
			}

			return $renderedResponse;
		}

//------------------------------------------------------------------------------------------------//
		protected function send( $data, $code = 0 )
		{
			$this->response['code'] = $code;
			$this->response['data'] = $data;

			$this->_render();
			exit();
		}

//------------------------------------------------------------------------------------------------//
		protected function sendCode( $code )
		{
			$this->response['code'] = $code;
			$this->_render();
			exit();
		}

//------------------------------------------------------------------------------------------------//
		protected function sendResponse( $data = null )
		{
			if( $data !== null )
			{
				$this->response = $data;
			}

			$this->_render();
			exit();
		}
	}
}