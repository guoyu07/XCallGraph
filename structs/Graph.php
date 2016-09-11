<?php

namespace Struct;

use Diskerror\Typed;

class Graph extends Typed\TypedClass
{
    protected $version = 0;
    protected $creator = '';
    protected $command = '';
    protected $part = 0;
    protected $positions = '';
    protected $events = '';
    protected $fileLineCount = 0;
    protected $nodes = '__class__\\Struct\\NodeList';
    protected $edges = '__class__\\Struct\\EdgeList';

    function _set_creator($in)
    {
        $this->creator = self::trim($in);
    }

    function _set_command($in)
    {
        $this->command = self::trim($in);
    }

    function _set_positions($in)
    {
        $this->positions = self::trim($in);
    }

    function _set_events($in)
    {
        $this->events = self::trim($in);
    }

    static function trim($s)
    {
        return trim($s, "\x00..\x20");
    }

}
