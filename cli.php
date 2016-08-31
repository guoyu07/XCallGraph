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

	$files = $opts->getRemainingArgs());

	//  Open cachegrind file.
	//  Parse data into objects.
	//  Convert objects into "dot" file.

	//  Struct\Node
	//  Struct\Edge

	//  Model\Parser

	new Struct\Node;
	new Struct\Edge;
	new Model\Parser;
}
catch( Zend\Console\Exception\InvalidArgumentException $e ) {
	echo $e->getUsageMessage();
	exit( $e->getCode() );
}
catch( Exception $e ) {
	echo $e, PHP_EOL;
	exit( $e->getCode() );
}
