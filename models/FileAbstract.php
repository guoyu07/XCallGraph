<?php

namespace Model;

abstract class FileAbstract
{
	private $_fileHandle = null;

	function __construct($fileName)
	{
        $this->_fileHandle = fopen($fileName, $this->_getMode());
        if (!$this->_fileHandle) {
            throw new \RuntimeException('bad input file');
        }
	}

	function __destruct()
	{
	    //  Constructor insures file handle is good.
        fclose($this->_fileHandle);
	}

	protected function _getHandle()
	{
        return $this->_fileHandle;
	}

	abstract protected function _getMode();
}
