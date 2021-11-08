<?php declare(strict_types=1);

$curDir = __DIR__ .DIRECTORY_SEPARATOR. '.' .DIRECTORY_SEPARATOR;
require_once $curDir . 'Main.php';
require_once $curDir . 'LMFoundry_autoload.php';
use LM\Foundry\{Cast, Utils, Results};

function runJson(string $dataFilename, string $moldFilename) : Results {
    // Read in the json data from the file
    // convert it to assoc array
    // call cast->pour() method
    $jsonAsArray = Utils::DecodeJsonFile($dataFilename);
    $results = Cast::pour($moldFilename, $jsonAsArray);
    return $results;
}

echo Main::main($argc, $argv, 'json', 'runJson');
