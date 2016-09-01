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

	$parser = Model\Parser::getInstance();
	$parser->setOptions($opts->toArray());
	$graph = $parser->exec($files[0], $files[1]);

	print_r($graph->toArray());

	//  Open cachegrind file.
	//  Parse data into objects.
	//  Convert objects into "dot" file.
	//  Convert "dot" file into "svg" file.
}
catch( Zend\Console\Exception\InvalidArgumentException $e ) {
    echo 'Usage: xcg [-h | [-i] input_file [ output_file ]', PHP_EOL;
	echo $e->getUsageMessage();
	exit( $e->getCode() );
}
catch( Exception $e ) {
	echo $e, PHP_EOL;
	exit( $e->getCode() );
}
