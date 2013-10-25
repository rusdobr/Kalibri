<?php

namespace Kalibri\Session\Handler;

interface BaseInterface
{
	public function close();
	public function destroy( $sessionid );
	public function gc( $maxlifetime );
	public function open( $save_path , $sessionid );
	public function read( $sessionid );
	public function write( $sessionid, $sessiondata );
}