<?php

namespace Kalibri\Cache;

class Exception extends \Kalibri\Exception
{
	public const FAILED_TO_GET = 2;
	public const FAILED_TO_CONNECT = 3;
	public const FAILED_TO_SET = 4;
}