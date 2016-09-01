<?php

namespace Struct;

use Diskerror\Typed;

class Edge extends Typed\TypedClass
{
    protected $caller = '';
    protected $callee = '';
    protected $lineNumber = 0;
    protected $params = [];
    protected $return = null;
}
