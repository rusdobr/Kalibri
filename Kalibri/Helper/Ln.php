<?php

namespace Kalibri\Helper;

class Ln implements \Kalibri\Helper\BaseInterface
{
    #[\Override]
    public static function init( array $options = null ){}
//------------------------------------------------------------------------------------------------//
    public static function tr( $key, array $params = null )
    {
        return \Kalibri::l10n()->tr( $key, $params );
    }

//------------------------------------------------------------------------------------------------//
    public static function current( $short = true )
    {
        return \Kalibri::l10n()->getCurrent( $short );
    }
}