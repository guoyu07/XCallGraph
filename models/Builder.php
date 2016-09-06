<?php

namespace Model;

class Builder extends \Diskerror\Utilities\Singleton
{
	function setOptions(array $opts)
	{

	}

	function exec(\Struct\Graph $graph, $fileName)
	{
		$file = new FileWriter($fileName);

		$file->writeLn('digraph "' . $graph->command . '" {');
		$file->writeLn('rankdir=LR;');
		$file->writeLn('node [fontsize=7,shape=box];');
		$file->writeLn('stylesheet="' . __DIR__ . '/../svg_style.css";');

		foreach ($graph->nodes as $node) {
			$file->write('"' . $node->functionName . '" [label="' . preg_replace('/(::|->)/', '\\n$1', $node->functionName) . '"');

			switch ($node->area) {
				case 'mage':
				$file->write(',shape=box,style=filled,fillcolor=gray92');
				break;

				case 'enterprise':
				$file->write(',shape=box');
				break;

				case 'local':
				case 'community':
				case '':
				$file->write(',shape=ellipse');
				break;

				case 'Mage':
				$file->write(',shape=hexagon,style=filled,fillcolor=orange1');
				break;
			}

			$file->writeLn('];');
		}

		foreach ($graph->edges as $name=>$edge) {
			$file->writeLn('"' . $edge->caller . '" -> "' . $edge->callee . '";');
		}

		$file->writeLn('}');
	}
}
