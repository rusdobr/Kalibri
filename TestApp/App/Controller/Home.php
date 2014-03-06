<?php

namespace Test\App\Controller;

class Home extends \Kalibri\Controller\Page
{
	public function index()
	{
		/**
		 * @todo: Home page code
		 */
		$this->page()->setContent("Hello World!");
	}

    public function about()
    {
        $this->autoFindView(true)->page()->setLayoutName('index');
    }
}