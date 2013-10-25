<?php

namespace Kalibri\Utils\Html
{
	class TextNode extends Node
	{
		protected $text;

		public function process()
		{
			$pos = strpos( $this->raw, '<' );

			$this->text = substr( $this->raw, 0, $pos );
			$this->raw = substr( $this->raw, $pos );
		}

		public function getText()
		{
			return $this->text;
		}

		public function dump()
		{
			return '<li>'.$this->text.'</li>';
		}
	}
}