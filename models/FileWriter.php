<?php

namespace Model;

class FileWriter extends FileAbstract implements WriterInterface
{
	protected function _getMode()
	{
	    return 'w';
	}

	function writeLn($s)
	{
		fwrite($this->_getHandle(), (string) $s . PHP_EOL);
	}

	function write($s)
	{
		fwrite($this->_getHandle(), (string) $s);
	}
}
