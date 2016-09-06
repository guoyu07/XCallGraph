<?php

namespace Model;

class Parser extends \Diskerror\Utilities\Singleton
{
// 	const STOP_REGEX = '/fn=include|fn=require|\\buc_words\\b|getSingleton|getModelInstance|Mage::|Mage_Core_Model_Resource|_callObserverMethod|\\bZend_|Varien|Autoload|autoload|Mage_Core_Model_Store->get(Code|Config)|Mage_Core_Model_Config->get(Node|Options)|Mage_Core_Model_App->getStore|Mage_Cache_Backend|Mage_Admin_Model_Session|Mage_Core_Model_App->(_initStores|getRequest|getFrontController)|Mage_Adminhtml_Controller_Action->preDispatch/';
	const STOP_REGEX = '/fn=include|fn=require|Autoload|autoload|\\buc_words\\b|\\bZend_|\\bVarien_(Profiler|Db)|Simplexml_Element/';

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
			//	Looking to start node.
			if ($node===null) {
				if ($line === 'fl=php:internal') {
					//	an empty line also evaluates to false
					while ($file->getLine())
						;
				}
				elseif (substr($line, 0, 3) === 'fl=') {
					$node = new \Struct\Node;
					$node->fileName = substr($line, 3);

					if (strpos($line, '/core/Mage/') !== false) {
						$node->area = 'mage';
					}
					elseif (strpos($line, '/core/Enterprise/') !== false) {
						$node->area = 'enterprise';
					}
					elseif (strpos($line, '/community/') !== false) {
						$node->area = 'community';
					}
					elseif (strpos($line, '/local/') !== false) {
						$node->area = 'local';
					}
					elseif (strpos($line, '/lib/') !== false) {
						$node->area = 'lib';
					}
					elseif (strpos($line, '/includes/') !== false) {
						$node->area = 'includes';
					}
					elseif (strpos($line, 'app/Mage.php') !== false) {
						$node->area = 'Mage';
					}

					//	The next line will begin with an "fn=".
					$line = $file->getLine();
					if (preg_match(self::STOP_REGEX, $line)) {
						$node = null;
						//	an empty line is also false
						while ($file->getLine())
							;
					}
					else {
						$node->functionName = substr($line, 3);
					}
				}
				elseif (substr($line, 0, 8) === 'summary:') {
					//	Stop processing because we're in the summary section near the end of the file.
					break;
				}
			}
			//	Currently have a node. Looking to start edge.
			else {
				if (substr($line, 0, 4) === 'cfl=') {
					if ($line === 'cfl=php:internal') {
						$file->getLine(3);
					}
					else {
						$edge = new \Struct\Edge;
						$edge->caller = $node->functionName;

						//	The next line will begin with a "cfn=".
						$line = $file->getLine();
						if (preg_match(self::STOP_REGEX, $line)) {
							$file->getLine(2);
						}
						else {
							$edge->callee = substr($line, 4);

							//	The next line starts with "calls=", so skip it and use the one after.
							$line = $file->getLine(2);
							$digits = explode(' ', $line);
							$edgeName = $edge->caller . '>>' . $edge->callee;
							if (isset($graph->edges[$edgeName])) {
								$graph->edges[$edgeName]->callCount++;
								$edge->runTime += $digits[1];
							}
							else {
								$edge->lineNumber = $digits[0];
								$edge->runTime = $digits[1];
								$graph->edges[$edgeName] = $edge;
							}
						}
					}
				}
				elseif ($line === '') {
					if (isset($graph->nodes[$node->functionName])) {
						$graph->nodes[$node->functionName]->callCount++;
					}
					else {
						$graph->nodes[$node->functionName] = $node;
					}
					$node = null;
				}
			}

			//////////////////////////////////////////////////////////////////
			$line = $file->getLine();

			//	End of file returns false.
			if ($line === false) {
				break;
			}
		}

		$graph->fileLineCount = $file->linesRead;

		//	Remove orphaned nodes.
		foreach ($graph->nodes as $node=>$v) {
			foreach ($graph->edges as $edge) {
				if ($node === $edge->caller || $node === $edge->callee) {
					continue 2;
				}
			}
			unset($graph->nodes[$node]);
		}

		return $graph;
	}
}
