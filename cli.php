<?php

/**
 * Convert all errors into ErrorExceptions
 */
set_error_handler(
	function ($severity, $errstr, $errfile, $errline) {
		throw new ErrorException($errstr, 1, $severity, $errfile, $errline);
	},
	E_USER_ERROR
);

try {
	require './vendor/autoload.php';

	$opts = new Zend\Console\Getopt([
		'help|h' => 'This help text.',
		'i' => 'Include internal PHP functions.'
	]);

	$files = $opts->getRemainingArgs();

	if (count($files)===0) {
		throw new RuntimeException('missing input file');
	}

	if (count($files)===1) {
		$files[] = $files[0] . '.dot';
	}

	if (count($files)>2) {
		throw new RuntimeException('too many input files');
	}

	echo 'Parsing XDebug/Cachegrind.', PHP_EOL;
	$parser = Model\Parser::getInstance()->setOptions($opts->toArray());
	$graph = $parser->exec(new Model\FileReader($files[0]));

	echo 'Building Graphviz (dot) data.', PHP_EOL;
	$builder = Model\Builder::getInstance()->setOptions($opts->toArray());
	$builder->exec($graph, new Model\FileWriter($files[1]));

	echo 'Converting DOT to SVG.', PHP_EOL;
	exec('dot -Tsvg ' . $files[1] . ' -O');
}
catch( Zend\Console\Exception\InvalidArgumentException $e ) {
	echo 'Usage: ./run [-h | [-i] input_file [ output_file ]', PHP_EOL;
	echo $e->getUsageMessage();
	exit( $e->getCode() );
}
catch( Exception $e ) {
	echo $e, PHP_EOL;
	exit( $e->getCode() );
}
