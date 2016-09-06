<?php

namespace Struct;

use Diskerror\Typed;

class Node extends Typed\TypedClass
{
    protected $fileName = '';
    protected $functionName = '';
    protected $area = '';
    protected $lineNumber = 0;
    protected $runTime = 0;
    protected $callCount = 1;
}
