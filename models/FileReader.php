<?php

namespace Model;

class FileReader
{
	protected $_fileHandle = null;
	protected $_line = null;
	protected $_linesRead = 0;

	function __construct($fileName)
	{
        $this->_fileHandle = fopen($fileName, 'r');
        if ($this->_fileHandle === null) {
            throw new Exception('bad input file');
        }
	}

	function __destruct()
	{
	    //  Constructor insures file handle is good.
        fclose($this->_fileHandle);
	}

	function getLine($skip=1)
	{
	    $skip = (int) $skip;

	    if ($skip < 0) {
	        throw new ErrorException('"skip" cannot be negative');
	    }
	    elseif ($skip === 0) {
	        return $this->_line;
	    }

	    while ($skip-- && $this->_line!==false) {
		    $this->_line = fgets($this->_fileHandle);
            $this->_linesRead++;
        }

		//	End of file returns false.
		if ($this->_line === false) {
            $this->_linesRead--;
			return false;
		}

		$this->_line = trim($this->_line, "\x00..\x20");

		return $this->_line;
	}

	function __get($key)
	{
	    switch ($key) {
	        case 'linesRead':
	        return $this->_linesRead;
	        break;

	        case 'line':
	        return $this->_line;
	        break;

	        default:
	        throw new ErrorException('property not found');
	    }
	}
}
