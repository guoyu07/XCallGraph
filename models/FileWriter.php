<?php

namespace Model;

class FileWriter
{
	protected $_fileHandle = null;

	function __construct($fileName)
	{
        $this->_fileHandle = fopen($fileName, 'w');
        if ($this->_fileHandle === null) {
            throw new RuntimeException('bad input file');
        }
	}

	function __destruct()
	{
	    //  Constructor insures file handle is good.
        fclose($this->_fileHandle);
	}

	function writeLn($s)
	{
		fwrite($this->_fileHandle, (string) $s . PHP_EOL);
	}

	function write($s)
	{
		fwrite($this->_fileHandle, (string) $s);
	}
}
