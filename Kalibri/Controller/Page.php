<?php

namespace Kalibri\Controller {

	class Page extends \Kalibri\Controller\Base
	{
		/**
		 * Flag shows is controller should find appropriate view it self
		 * @var bool
		 */
		protected $_autoFindView = false;

		/**
		 * Kalibri\Page instance
		 * @var \Kalibri\View\Page
		 */
		protected $_page = null;

//------------------------------------------------------------------------------------------------//
		#[\Override]
  public function _render( $asString = false )
		{
			if( $this->_autoFindView && $this->page()->getViewName() == null )
			{
				$this->_page->setViewName( $this->_page->findView() );
			}

			$this->_isRendered = true;

			if( $this->_page )
			{
				return $this->_page->render( $asString );
			}
		}

//------------------------------------------------------------------------------------------------//
		public function autoFindView( $mode = null )
		{
			if( $mode !== null )
			{
				$this->_autoFindView = (bool) $mode;
			}

			return $this->_autoFindView;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get controller page instance
		 * 
		 * @return \Kalibri\View\Page
		 */
		public function &page()
		{
			if( !$this->_page )
			{
				$this->_page = \Kalibri::page( new \Kalibri\View\Page );
			}

			return $this->_page;
		}
	}
}