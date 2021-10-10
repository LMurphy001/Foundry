<?php declare(strict_types=1);

$curDir = __DIR__ .DIRECTORY_SEPARATOR. '.' .DIRECTORY_SEPARATOR;
require_once $curDir. 'LMFoundry_autoload.php';
use LM\Foundry\{Results};

class Main {
    static function main(int $argc, array $argv, string $type, callable $callback) : Results {
        if ($argc != 3 || !file_exists($argv[1]) || !file_exists($argv[2]) ) {
            return SELF::usageAndDie($argv[0], $type);
        } else {
            $curDir = __DIR__ .DIRECTORY_SEPARATOR. '.' .DIRECTORY_SEPARATOR;
            $firstArg = $curDir . $argv[1];
            $secondArg = $curDir . $argv[2];
            return $callback($firstArg, $secondArg);
        }
    }

    static function usageAndDie(string $progName, string $type) : string{
        $ustr = "Usage: php {$progName} {$type}_data_filename mold_filename";
        error_log($ustr);
        exit($ustr);
        return $ustr;
    }
}
