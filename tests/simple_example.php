<?php declare(strict_types=1);

require __DIR__ .DIRECTORY_SEPARATOR. '.' .DIRECTORY_SEPARATOR. 'LMFoundry_autoload.php';
use LM\Foundry\{Config, Cast};

class SimpleExample{
    function __construct()
    {
        $moldFile = Config::getMoldDir() . 'simple_example.txt';
        $dict = array( 'name'=>'apple', 'color'=>'red', 'price'=>'1.20' );
        $contents = Cast::pour($moldFile, $dict);

        if (!$contents->hasError()) {
            echo PHP_EOL.'The results of pouring the \'apple\' data into the mold are:' .PHP_EOL. $contents->getInfo() .PHP_EOL;
        } else {
            echo $contents->getError() . PHP_EOL;
        }
    }
}
new SimpleExample;
