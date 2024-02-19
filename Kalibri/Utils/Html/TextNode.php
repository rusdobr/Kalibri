<?php

namespace Kalibri\Utils\Html
{
	class TextNode extends Node
	{
		protected $text;

		#[\Override]
  public function process( Document &$document ): void
		{
			$pos = strpos( (string) $this->raw, '<' );

			$this->text = substr( (string) $this->raw, 0, $pos );
			$this->raw = substr( (string) $this->raw, $pos );
            unset( $this->document );
            unset( $this->raw );
		}

		#[\Override]
  public function getText()
		{
			return $this->text;
		}

        public function setText($text)
        {
            $this->text = $text;
            return $this;
        }

        #[\Override]
        public function toHtml()
        {
            return ' '.$this->getText().' ';
        }

        #[\Override]
        public function getName()
        {
            return 'text';
        }

		#[\Override]
  public function dump()
		{
			return '<li>'.$this->text.'</li>';
		}
	}
}