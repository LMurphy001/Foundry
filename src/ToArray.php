<?php
declare(strict_types=1);
namespace LM\Foundry;
/**
 *  ToArray Class: Convert various things into an array of associative arrays, i.e. 
 *  The returned array will be a list of arrays with name=>value pairs.
 **/
 class ToArray {
     /*static private function fixPropertyNames(string $className, array $assocArray) : array {
         // ?use ClassName::class to get $className ?
        $newArr = array();
        foreach ($assocArray as $name => $value) {
            if (str_starts_with($name, "\0*\0")) {
                $newName = substr($name, 3);
            } elseif (str_starts_with($name, "\0".$className."\0" )) {
                $offset = strlen($className) + 2;
                $newName = substr($name, $offset);
            } else {
                $newName = $name;
            }
            $newArr[$newName] = $value;
        }
        return $newArr;
    }*/
    static public function Object(object $obj) : array {
        //$array = (array)$obj;
        //$array = SELF::fixPropertyNames(get_class($obj), $array);
        $array = get_object_vars($obj);
        $array = array($array);
        return $array;
    }

    static public function ListOfObjects(array $objList) : array {
        $array = array();
        foreach ($objList as $obj) {
            if (is_object($obj)) {
                //$aArray = (array)$obj;
                //$fixedNames = SELF::fixPropertyNames(get_class($obj), $aArray);
                //$array[] = $fixedNames;
                $array[] = get_object_vars($obj);
            }
            elseif (is_array($obj)) {
                if (Utils::isDict($obj)){
                    $array[] = $obj;
                } else throw new \Exception("List of Objects contains non-associative-array");
            }
            else throw new \Exception("List Of Objects contains something other than an object or an array");
        }
        return $array;
    }

    static public function CSVFile(string $csvFilename) : array {
        $handle = fopen($csvFilename, "r");
        $rowNum = 0;
        $array = array();
        $names = array();
        while (($data = fgetcsv($handle)) !== FALSE) {
            $rowNum++;
            if ($rowNum == 1) {
                $names = $data; // The first fetched will be the keys in the key=>value pairs of returned array
            } else {
                $thisRow = array();
                for ($i = 0; $i < count($names); $i++)
                    if (isset($names[$i]) && isset($data[$i]))
                        $thisRow[$names[$i]] = $data[$i];
                $array[] = $thisRow;
            }
        }
        return $array;
    }

    static public function JsonStr(string $jsonStr) : array {
        $decoded = json_decode($jsonStr, associative:true );
        if (! is_array($decoded)) 
            throw new \Exception("Expecting a json string to convert to array");
        if (Utils::isDict($decoded)) {
            return array( $decoded );
        } else {
            return $decoded;
        }
    }

    static public function JsonFile(string $jsonFilename) : array {
        $results = Utils::getFileContents($jsonFilename);
        if ($results->hasError()) throw new \Exception("Unable to get contents of Json file");
        return SELF::JsonStr($results->getInfo());
    }

    static public function PDOFetch(mixed $pdoFetchResult) : array {
        return array();
    }

    static public function PDOFetchAll(array $pdoFetchAllResult) : array {
        return array();
    }
}
