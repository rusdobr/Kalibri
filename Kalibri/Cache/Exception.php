<?php

namespace Kalibri\Cache;

class Exception extends \Kalibri\Exception
{
	const FAILED_TO_GET = 2;
	const FAILED_TO_CONNECT = 3;
	const FAILED_TO_SET = 4;
}