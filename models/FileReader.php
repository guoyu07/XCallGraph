<?php

namespace Model;

class FileReader extends FileAbstract implements ReaderInterface
{
	protected $_line = null;
	protected $_linesRead = 0;

	protected function _getMode()
	{
	    return 'r';
	}

	function getLine($skip=1)
	{
	    $skip = (int) $skip;
	    if ($skip < 0) {
	        throw new RuntimeException('"skip" cannot be negative');
	    }

	    if ($skip === 0) {
	        return $this->_line;
	    }

	    while ($skip-- && $this->_line!==false) {
		    $this->_line = fgets($this->_getHandle());
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
	        throw new RuntimeException('property not found');
	    }
	}
}
