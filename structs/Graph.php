<?php

namespace Struct;

use Diskerror\Typed;

class Graph extends Typed\TypedClass
{
	protected $creator = '';
	protected $command = '';
	protected $version = 0;
	protected $part = 0;
	protected $positions = '';
	protected $events = '';
	protected $nodes = '__class__\\Struct\\NodeList';
	protected $edges = '__class__\\Struct\\EdgeList';

	function _set_creator($in)
	{
	    $this->creator = trim( preg_replace('/^creator:(.*)$/', '$1', $in) );
	}

	function _set_command($in)
	{
	    $this->command = trim( preg_replace('/^cmd:(.*)$/', '$1', $in) );
	}

	function _set_version($in)
	{
	    $this->version = (int) trim( preg_replace('/^version:(.*)$/', '$1', $in) );
	}

	function _set_part($in)
	{
	    $this->part = (int) trim( preg_replace('/^part:(.*)$/', '$1', $in) );
	}

	function _set_positions($in)
	{
	    $this->positions = trim( preg_replace('/^positions:(.*)$/', '$1', $in) );
	}

	function _set_events($in)
	{
	    $this->events = trim( preg_replace('/^events:(.*)$/', '$1', $in) );
	}

}
