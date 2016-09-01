<?php

namespace Model;

class Parser extends \Diskerror\Utilities\Singleton
{
    protected $_useInternal = false;

    function setOptions(array $opts)
    {

    }

    function exec($inputFile, $outputFile)
    {
        $inputFileHandle = fopen($inputFile, 'r');
//      $outputFileHandle = fopen($outputFile, 'w');

        //  Pull metadata from file.
        $graph = new \Struct\Graph;
        $lc = 0;
        while (1) {
            $line = self::_getLine($inputFileHandle);
            $lc++;

            //  If we ran out of file here then there's something wrong.
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

        //  Gather nodes (and edges soon).
        $node = null;
        while (1) {
            $m = [];    //  match values
            if ($line === 'fl=php:internal') {
                //  an empty line is also false
                while ($line = self::_getLine($inputFileHandle))
                    ;
            }
            elseif (substr($line, 0, 10) === 'fn=include' || substr($line, 0, 10) === 'fn=require') {
                $node = null;
                //  an empty line is also false
                while ($line = self::_getLine($inputFileHandle))
                    ;
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
            elseif (substr($line, 0, 3) === 'fl=') {
                $node = new \Struct\Node;
                $node->fileName = substr($line, 3);
            }
            elseif (substr($line, 0, 3) === 'fn=') {
                $node->functionName = substr($line, 3);
            }
            elseif (preg_match('/^([0-9]+) ([0-9]+)/', $line, $m)) {
                if (count($m) > 2) {
                    $node->lineNumber = $m[1];
                    $node->runTime = $m[2];
                }
            }
            elseif (substr($line, 0, 8) === 'summary:') {
                break;
            }

            $line = self::_getLine($inputFileHandle);
            $lc++;

            //  End of file returns false.
            if ($line === false) {
                break;
            }
        }

        return $graph;
    }

    static function _getLine($fp)
    {
        $line = fgets($fp);

        //  End of file returns false.
        if ($line === false) {
            return false;
        }

        return trim($line, "\x00..\x20");
    }
}
