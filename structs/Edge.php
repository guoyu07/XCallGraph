<?php

namespace Struct;

use Diskerror\Typed;

class Edge extends Typed\TypedClass
{
	protected $caller = '';
	protected $callee = '';
	protected $lineNumber = 0;
	protected $runTime = 0;
	protected $callCount = 1;
// 	protected $params = [];
// 	protected $return = null;
}
