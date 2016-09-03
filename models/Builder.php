<?php

namespace Model;

class Builder extends \Diskerror\Utilities\Singleton
{
	function setOptions(array $opts)
	{

	}

	function exec(\Struct\Graph $graph, $fileName)
	{
		$file = fopen($fileName, 'w');
		fwrite($file, 'digraph "' . $graph->command . '" {' . PHP_EOL);

		foreach ($graph->nodes as $node) {
			$label = preg_replace('/(::|->)/', '\\n$1', $node->functionName);
			fwrite($file, '"' . $node->functionName . '" [label="' . $label . '"];' . PHP_EOL);
		}

		foreach ($graph->edges as $name=>$edge) {
			fwrite($file, '"' . $edge->caller . '" -> "' . $edge->callee . '";' . PHP_EOL);
		}

		fwrite($file, '}' . PHP_EOL);
	}
}
