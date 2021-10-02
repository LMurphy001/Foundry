<?php
declare(strict_types=1);
namespace LM\Foundry;
/**
 * Utility functions which could be used in multiple places. All methods are static. No need to instantiate Utils.
 * Example of calling static method: $nameIsGood = Utils::isLegalVarName($name);
 */
class Utils {
    static public function getFileContents(string $fileName) : Results {
        // This function uses file_get_contents, which might fail if the file is locked. It doesn't use flock.
        // TODO: Lookup reading-locked-files-in-php and instead use that instead of file_get_contents.
        // Why? Because we are only reading the contents. Writing the contents would require a lock; reading shouldn't.
        $retVal = new Results;

        // Detect attempts to open schema:// instead of an actual file.
        $pattern = "#\A([A-Za-z][\+\-\.0-9A-Za-z_]*[:][/][/])#";
        $matchRes = preg_match_all( $pattern, $fileName, $matches);
        if ($matchRes > 0) {
            $retVal->addToError('getFileConents: attempt to open non-file, ' . $fileName . PHP_EOL);
            return $retVal;
        }

        if (!file_exists($fileName)) {
            $retVal->addToError ( "Missing file: $fileName" );
        } elseif (is_dir($fileName)) {
            $retVal->addToError ( "Can't read $fileName, is a directory" );
        } else {
            $got = file_get_contents($fileName);
            if ($got === false) {
                $retVal->addToError( "Unable to read file '$fileName'" );
            } else {
                $retVal->addToInfo ( $got );
            }
        }
        return $retVal;
    }

    /*static function isLegalVarName($varName) : bool
    {
        // Return true if and only if varName is a string which is a legal PHP variable name
        $pattern = "/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/";
        return ($varName != "this") && (1 === preg_match_all($pattern, $varName));
    }*/

    /*static function getMatches($matches, string $prefix, string $suffix) {
        $prefLen = strlen($prefix);
        $suffLen = strlen($suffix);
        $totLen = $prefLen + $suffLen;
        $resArray = array();
        if ( is_array($matches) /*(strval(gettype($matches)) == "array")* / &&
             (count($matches) >= 1) &&
             is_array($matches[0]) &&
             /*(strval(gettype($matches[0])) == "array") && * /
             (count($matches[0]) > 0) ) {
                foreach ($matches[0] as $value) {
                    /* For each variable appearing in the mold file as {$variable_name} : * /
                    if (str_starts_with($value, $prefix) &&
                        ( ($suffLen==0) || str_ends_with($value, $suffix))) {
                        $resArray[] = substr($value, $prefLen, strlen($value) - $totLen );
                    }
                }
            }
        return $resArray;
    }*/

    /*static function getPlaceholders(string $inputStr) : array {
        /* Looking for {$variable} where variable is a legal PHP variable name
         * Was: Looking for <?=$variable?> where variable is a legal PHP variable name
         * /
        /* Return an array of strings without the {$}. * /
        //$pattern = "/[<][?][=][$][a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*[?][>]/";
        $pattern = '#{([$][a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)}#';
        preg_match_all($pattern, $inputStr, $matches);
        return self::getMatches($matches, '{$', '}' );
    }*/

    /**
    * From https://stackoverflow.com/a/19404373
    * A function to fill the mold with variables, returns filled mold.
    *
    * @param string $mold  A mold with variables placeholders {$variable}.
    * @param array $variables A key => value store of variable names and values.
    *
    * @return string
    */ /*
    static function replaceVariablesInMold(string $mold, array $variables) :string|null {
        /* Original pattern; it allows any character inside the {} * /
        // $pattern = '#{(.*?)}#';
        /* Improved pattern, restricts the match to a PHP variable name. * /
        $pattern = '#{([$][a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)}#';
        $str = preg_replace_callback(
                $pattern,
                function($match) use ($variables) {
                    $match[1] = trim($match[1], '$');
                    return $variables[$match[1]];
                },
                ' ' . $mold . ' ');
        if (! is_null($str) ) {
            // Remove the leading and trailing spaces:
            $str = substr($str, 1, strlen($str)-2);
        }
        return $str;
   }
    */ /*
    static function isDict(array $array) : bool {
        /**
         * Check to see if $array is a dictionary with every key being a string.
         * Note: An empty array will return true!
         *
         * Rather than look at all of the keys of $array, return false as soon as
         * there's a non-string key. No need to look at all of them.
         *
         * If you need something else, see STACKOVERFLOW question:
         *       How to check if PHP array is associative or sequential?
         * /
        foreach ($array as $key=>$val) {
            if (!is_string($key))
                return false;
        }
        return true;
    } */

    static public function CSVFileToArray(string $csvFilename) : array {
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

    static public function DecodeJsonFile(string $jsonFilename) : mixed {
        $results = Utils::getFileContents($jsonFilename);
        if ($results->hasError()) throw new \Exception("Unable to get contents of Json file $jsonFilename");
        return json_decode($results->getInfo());
    }


}
