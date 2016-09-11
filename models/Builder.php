<?php

namespace Model;

class Builder extends \Diskerror\Utilities\Singleton
{
	function setOptions(array $opts)
	{
		//	handle the options

		return $this;
	}

	function exec(\Struct\Graph $graph, WriterInterface $output)
	{
		$output->writeLn('digraph "' . $graph->command . '" {');
		$output->writeLn('rankdir=LR;');
		$output->writeLn('node [fontsize=8,shape=box];');
		$output->writeLn('stylesheet="' . __DIR__ . '/../svg_style.css";');

		foreach ($graph->nodes as $node) {
			$output->write('"' . $node->functionName . '" [label="' . preg_replace('/(::|->)/', '\\n$1', $node->functionName) . '"');

			switch ($node->area) {
				case 'mage':
				$output->write(',shape=box,style=filled,fillcolor=gray92');
				break;

				case 'enterprise':
				$output->write(',shape=box');
				break;

				case 'local':
				case 'community':
				case '':
				$output->write(',shape=ellipse');
				break;

				case 'Mage':
				$output->write(',shape=hexagon,style=filled,fillcolor=orange1');
				break;
			}

			$output->writeLn('];');
		}

		foreach ($graph->edges as $name=>$edge) {
			$output->writeLn('"' . $edge->caller . '" -> "' . $edge->callee . '";');
		}

		$output->writeLn('}');
	}
}
