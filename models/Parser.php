<?php

namespace Model;

class Parser extends \Diskerror\Utilities\Singleton
{
	const STOP_REGEX = '/fn=include|fn=require|getSingleton|getModelInstance|Mage::|Mage_Core_Model_Resource|_callObserverMethod|\bZend_|\bVarien_|Autoload|autoload/';

	protected $_useInternal = false;
	protected $_lineCount = 0;

	function setOptions(array $opts)
	{

	}

	function exec($inputFileName)
	{
		$file = new FileReader($inputFileName);

		//	Pull metadata from file.
		$graph = new \Struct\Graph;
		while (1) {
			$line = $file->getLine();

			//	If we ran out of file here then there's something wrong.
			if ($line === false) {
				throw new \RuntimeException('file ended early');
			}

			$line = trim($line, "0x00..0x20");

			switch (substr($line, 0, 3)) {
				case 'ver':
				$graph->version = $line;
				break;

				case 'cre':
				$graph->creator = $line;
				break;

				case 'cmd':
				$graph->command = $line;
				break;

				case 'par':
				$graph->part = $line;
				break;

				case 'pos':
				$graph->positions = $line;
				break;

				case 'eve':
				$graph->events = $line;
				break;

				case 'fl=':
				break 2;

				default:
				break;
			}
		}

		//	Gather nodes and edges.
		$node = null;
		$edge = null;
		while (1) {
			$m = [];	//	match values
			if ($line === 'fl=php:internal') {
				//	an empty line is also false
				while ($line = $file->getLine())
					;
			}
			elseif (preg_match(self::STOP_REGEX, $line)) {
				$node = null;
				//	an empty line is also false
				while ($line = $file->getLine())
					;
			}
			elseif (substr($line, 0, 4) === 'cfl=') {
				if ($line === 'cfl=php:internal') {
					$file->getLine(3);
				}
				else {
					$edge = new \Struct\Edge;
					$edge->caller = $node->functionName;
				}
			}
			elseif (substr($line, 0, 4) === 'cfn=') {
				$ln11 = substr($line, 0, 11);
				if ($ln11 === 'cfn=include' || $ln11 === 'cfn=require') {
					$edge = null;
					$file->getLine(2);
				}
				else {
					$edge->callee = substr($line, 4);
				}
			}
			elseif (substr($line, 0, 3) === 'fl=') {
				$node = new \Struct\Node;
				$node->fileName = substr($line, 3);
			}
			elseif (substr($line, 0, 3) === 'fn=') {
				$node->functionName = trim(substr($line, 3));
			}
			elseif (preg_match('/^([0-9]+) ([0-9]+)/', $line, $m)) {
				if (count($m) > 2) {
					if ($edge !== null ) {
						if ($edge->callee!=='') {
							$edgeName = $edge->caller . '>>' . $edge->callee;
							if (isset($graph->edges[$edgeName])) {
								$graph->edges[$edgeName]->callCount++;
							}
							else {
								$edge->lineNumber = $m[1];
								$edge->runTime = $m[2];
								$graph->edges[$edgeName] = $edge;
							}
						}
						$edge = null;
					}
					else {
						$node->lineNumber = $m[1];
						$node->runTime = $m[2];
					}
				}
			}
			elseif ($line === '') {
				if ($node !== null && $node->functionName !== '') {
					if (isset($graph->nodes[$node->functionName])) {
						$graph->nodes[$node->functionName]->callCount++;
					}
					else {
						$graph->nodes[$node->functionName] = $node;
					}
				}
				$node = null;
			}
			elseif (substr($line, 0, 8) === 'summary:') {
				break;
			}

			//////////////////////////////////////////////////////////////////
			$line = $file->getLine();

			//	End of file returns false.
			if ($line === false) {
				break;
			}
		}

		$graph->fileLineCount = $file->linesRead;

		return $graph;
	}
}
