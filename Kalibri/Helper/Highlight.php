<?php

namespace Kalibri\Helper {

	class Highlight implements \Kalibri\Helper\BaseInterface
	{
		public static function init( array $options = null ){}
//------------------------------------------------------------------------------------------------//
		public static function php( $code, $lines_number = 0, $firstLine = 0, $currentLine = 0 )
		{
			if( !is_array( $code) )
				$code = explode("\n", $code);

			$count_lines = count( $code );

			$r1 = '';

			 if ($lines_number){           
					$r1 .= "<div class=\"linenum\">";
					foreach($code as $line =>$c) {
						if( $count_lines == 1 )
							$r1 .= "1<br />";
						else
							$r1 .= ($line+1)."<br />";
					 }
					 $r1 .= "</div>";
			 }

			 $r2 = "<div class=\"linetext\">";
			 $r2 .= highlight_string( implode( "\n",$code ), 1);
			 $r2 .= "</div>";

			return "<div class=\"code\">".$r1.$r2."</div>\n";
		}

//------------------------------------------------------------------------------------------------//
		public static function php1( $str, $withLines = false, $firstLine = 0, $currentLine = 0 )
		{
			// The highlight string function encodes and highlights
			// brackets so we need them to start raw
			$str = str_replace(array('&lt;', '&gt;'), array('<', '>'), $str);

			// Replace any existing PHP tags to temporary markers so they don't accidentally
			// break the string out of PHP, and thus, thwart the highlighting.

			$str = str_replace(array('<?', '?>', '<%', '%>', '\\', '</script>'),
								array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'), $str);

			// The highlight_string function requires that the text be surrounded
			// by PHP tags, which we will remove later
			$str = '<?php '.$str.' ?>'; // <?

			// All the magic happens here, baby!
			$str = highlight_string( $str, true );

			// Remove our artificially added PHP, and the syntax highlighting that came with it
			$str = preg_replace('/<span style="color: #([A-Z0-9]+)">&lt;\?php(&nbsp;| )/i', '<span style="color: #$1">', $str);
			//$str = preg_replace('/(<span style="color: #[A-Z0-9]+">.*?)\?&gt;<\/span>\n<\/span>\n<\/code>/is', "$1</span>\n</span>\n</code>", $str);
			//$str = preg_replace('/<span style="color: #[A-Z0-9]+"\><\/span>/i', '', $str);

			// Replace our markers back to PHP tags.
			$str = str_replace(array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
								array('&lt;?', '?&gt;', '&lt;%', '%&gt;', '\\', '&lt;/script&gt;'), $str);

			//var_dump( $str );exit();

			if( $withLines )
			{
				$str = substr( substr( $str, 0, -15 ), 35 );
				$str = trim( str_replace('<br /></span>', '</span><br />', str_replace('<br /><br /></span>', '</span><br /><br />',  $str ) ) );

				$str1 = '<span style="color: #000000"><table>';

				foreach( explode( '<br />', $str ) as $l=>$line )
				{
					//$str1 .= '<tr><td>'.($l+$firstLine).'</td><td>'.$line."</td></tr>\n";
					$str1 .= $line."\n";
				}

				$str1 .= '</table></span>';

				return $str1;
			}

			return $str;
		}

//------------------------------------------------------------------------------------------------//
		public static function phrase($str, $phrase, $tag_open = '<strong>', $tag_close = '</strong>')
		{
			if ($str == '')
			{
				return '';
			}

			if ($phrase != '')
			{
				return preg_replace('/('.preg_quote($phrase, '/').')/i', $tag_open."\\1".$tag_close, $str);
			}

			return $str;
		}
	}
}