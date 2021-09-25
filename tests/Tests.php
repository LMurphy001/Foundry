<?php declare(strict_types=1);
require __DIR__ . DIRECTORY_SEPARATOR . 'Fruit.php';

use LM\Foundry\{Cast,ToArray,Config};

class Tests {
    static function testFormat($format) : void {
        /* Generate the given format for each type of input data */
        $s = '';

        $s .= Tests::singleFruit($format);
        $s .= Tests::multipleFruit($format);

        $s .= Tests::singleObject($format);
        $s .= Tests::objectList($format);

        $s .= Tests::json_file($format);
        $s .= Tests::json_str1($format);
        $s .= Tests::json_str2($format);

        $s .= Tests::csv_file($format);

        $s .= Tests::pdo_fetchall($format);

        // All tests are complete for this format. The results have been accumulated in $s.

        //Put the pieces together, i.e. 'chain' the results from the other molds as input to the $format_doc mold.
        $moldFile = Config::getMoldDir() . $format.'_doc';
        $docArray = array(
            'doc_title'       => strtoupper($format).' FORMAT TESTS',
            'doc_head_styles' => '', // E.g. Could put css styles here.
            'doc_contents'    => $s
        );
        $docResults = Cast::pour($moldFile, $docArray, false);

        if ($docResults->hasError()) {
            error_log("Errors encountered while filling {$format}_doc mold.\n" . $docResults->getError());
        }
        else {
            // This echoes the final result, but it could be placed somewhere else.
            echo $docResults->getInfo();
        }
        echo PHP_EOL.PHP_EOL;
    }

    private static function run(string $testName, array $arr, string $generateFormat, string $fileExt='') : string
    {
        $resStr = '';
        if (strlen($fileExt)>0) {
            $fileExt = '.' . $fileExt;
        }
        $moldFile = Config::getMoldDir() . $generateFormat . '_row' . $fileExt;
        $results = Cast::pour($moldFile, $arr);

        if ($results->hasError()) {
            error_log("TEST '" . $testName ."' FOR '" . $generateFormat . "' FORMAT, " .
                "ERROR(S)." .PHP_EOL. $results->getError() .PHP_EOL);
        }
        if ($results->hasInfo()) {
            $rows = $results->getInfo();
            // Put a "table" wrapper around the generated rows:
            $moldFile = Config::getMoldDir() . $generateFormat . '_table' . $fileExt;
            $results = Cast::pour(
                $moldFile,
                array('caption'=>"TEST: $testName", 'rows'=>$rows),
                false);

            if ($results->hasError()) {
                error_log( $results->getError() );
            }
            $resStr .= $results->getInfo(); // Accumulate results in $resStr
        }
        return $resStr;
    }

    static function singleFruit(string $generateFormat) : string {
        $singleFruit = array("name"=>"Red Delicious", "color"=>"red", "price"=>1.25);
        // We need to pass an array of associative arrays:
        $singleFruit = array( $singleFruit );
        return SELF::run("singleFruit", $singleFruit, $generateFormat);
    }
    static function multipleFruit(string $generateFormat) : string {
        /* THIS TEST CONTAINS INTENTIONAL ERRORS!
        1. 'fruity->name' is not a valid variable name.
        2. 'banana' is missing a price.
        These two things SHOULD an error. */ 
        $fruitList = array (
            array( "color"=>"green",  "name"=>"grapes", "price"=>"1.50", '$fruity->name'=>"juicy"),
            array( "color"=>"red",    "name"=>"red delicious", "price"=>"1.00", 'num'=>"100"),
            array( "color"=>"purple", "name"=>"plum", "price"=>"1.20", "favorite"=>true),
            array( "color"=>"yellow", "name"=>"banana"),
            array( "color"=>"orange", "name"=>"papaya", "price"=>"3.50" ) );
        return SELF::run("multipleFruit", $fruitList, $generateFormat);
    }
    static function singleObject(string $generateFormat) : string {
        $banana = new Fruit("Banana", "yellow", "0.85");
        $arr = ToArray::Object($banana);
        return SELF::run("singleObject",  $arr, $generateFormat);
    }
    static function objectList( string $generateFormat) : string {
        $banana = new Fruit("Banana", "yellow", "0.85");
        $kiwi = new Fruit("Kiwi", "green", "2.50");
        $pomegranate = new Fruit("Pomegranate", "red", "3.99");
        $fruitArray = array ( $kiwi, $banana, $pomegranate);
        $arr = ToArray::ListOfObjects($fruitArray);
        return SELF::run("objectList", $arr, $generateFormat );
    }
    static function json_file(string $generateFormat) : string {
        $jsonFile = __DIR__ . DIRECTORY_SEPARATOR. 'data' .DIRECTORY_SEPARATOR. 'fruit.json';
        return SELF::run("json_file", ToArray::JsonFile($jsonFile), $generateFormat);
    }
    static function json_str1(string $generateFormat) : string {
        $jsonStr = '{ "color":"orange", "name":"papaya", "price":"3.50" }';
        return SELF::run("json_str1", ToArray::JsonStr($jsonStr), $generateFormat);
    }
    static function json_str2(string $generateFormat) : string {
        $jsonStr = '{ "color":"orange", "name":"papaya", "price":"3.50" }';
        $jsonStr2 = '{ "color":"purple", "name":"plum", "price":"1.20" }';
        $jsonArrStr = '[' .$jsonStr. ', ' .$jsonStr2. ']';
        return SELF::run("json_str2", ToArray::JsonStr($jsonArrStr), $generateFormat);
    }
    static function csv_file(string $generateFormat) : string {
        $csvFile = __DIR__.DIRECTORY_SEPARATOR. 'data' .DIRECTORY_SEPARATOR. 'fruit.csv';
        return SELF::run("csv_file", ToArray::CSVFile($csvFile), $generateFormat);
    }
    static function pdo_fetchall(string $generateFormat) : string {
        $dbFile = __DIR__.DIRECTORY_SEPARATOR.'data' . DIRECTORY_SEPARATOR . 'fruit.sqlite';
        $db = new \PDO('sqlite:' . $dbFile );
        if ($db) {
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(\PDO::ATTR_TIMEOUT, 15);
            $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $sth = $db->prepare("SELECT name, color, price FROM fruit");
            $sth->execute();
            $array = $sth->fetchAll();
            return SELF::run('pdo_fetchall', $array, $generateFormat);
        } else {
            error_log("Failed to open db: $dbFile\n");
        }
        return '';
    }


}
