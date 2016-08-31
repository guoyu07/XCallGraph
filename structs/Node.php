<?php

namespace Struct;

use Diskerror\Typed;

class Node extends Typed\TypedClass
{
	protected $fileName = '';
	protected $functionName = '';
	protected $lineNumber = 0;
}
