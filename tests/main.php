<?php
require __DIR__ .DIRECTORY_SEPARATOR. '.' .DIRECTORY_SEPARATOR. 'LMFoundry_autoload.php';
require __DIR__ .DIRECTORY_SEPARATOR. '.' .DIRECTORY_SEPARATOR. 'Tests.php';

if ($argc == 2) {
    switch ($argv[1]) {
        case 'html' :
        case 'md' :
            Tests::testFormat($argv[1]);
            break;
        default:
            usageAndDie($argc, $argv);
    }
} else {
    usageAndDie($argc, $argv);
}
function usageAndDie($numArg, $args) {
    error_log("Usage: php " . $args[0] . ' {md | html}');
}
