<?php

namespace Model;

interface WriterInterface
{
	function writeLn($s);

	function write($s);
}
