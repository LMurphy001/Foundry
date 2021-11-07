<?php
declare(strict_types=1);
namespace LM\Foundry;
class Cast
{
    static function pour(string $moldName, array $liquid, bool $useHtmlSpecialChars=true, int $depth=0 ) : Results
    {
        $results = Utils::getFileContents($moldName); // Get the contents of the mold file
        if ($results->hasError())
            return $results;
        if (!is_array($liquid)) {
            $results = new Results;
            $results->setError('Pour(): Expecting second parameter to be an array' . PHP_EOL);
            return $results;
        }
        if (!Utils::isDict($liquid)) {
            $results = new Results();
            $results->setError('Pour(): Expecting second parameter to be associative array, not ' . gettype($liquid) . PHP_EOL);
            return $results;
        }

        $moldString = $results->getInfo();
        $dataNames = array_keys($liquid); // These are the names in the data file.

        $pat = '[a-zA-Z][0-9a-zA-Z\x5f]';
        // Regular placeholders: {$ followed by at least one letter followed by letters, numbers, and underscore, ending with }
        $regPattern = '#{([$]' . $pat . '*)}#';
        $regPlaceHolders = Utils::getPlaceholders($moldString, $regPattern);
        $regPlaceHolders = Utils::getMatches($regPlaceHolders, '{$', '}' );

        $newString = $moldString;
        foreach ($regPlaceHolders as $rph) {
            if (in_array($rph, $dataNames)) {
                if ( is_scalar($liquid[$rph]) || is_null($liquid[$rph]) ) {
                    $newString = str_replace( '{$'.$rph.'}',
                        $useHtmlSpecialChars ?  htmlspecialchars( strval($liquid[$rph] )) : strval($liquid[$rph] ),
                        $newString );
                } else {
                    $results->addToError($rph. ' value can\'t be printed as a string. ');
                    // What to do if if the data value is complex? E.g. Array, Object, other...
                }
            } else { // It's okay if there was no data for the place holder. The output will just have the {$name} in it
                $results->addToError($moldName.' Unable to find \'' . $rph. '\' in liquid key names. ' );   
            }
        }
        // $newString now contains ordinary string placeholders

        // x24 = $   x20 = a space   . = any character   x27 = single quote (apostrophe)
        $pourPattern = '#{(pour' . '\x20+' .'\x24' . $pat .'*[\x20][\x20]*' . '[\x27][\x20-\x7e]+[\x27]' . '.*)}#';
        $pourPlaceHolders = Utils::getPlaceholders($moldString, $pourPattern);
        $pourPlaceHolders = Utils::getMatches($pourPlaceHolders, '{pour ', '}' ); // flatten, strip off '{pour ...}'
        $newPlaceHolders = [];
        foreach ($pourPlaceHolders as $value) {
            $newItem = array_filter(explode(' ', trim($value, ' ') ) ); // get rid of empty strings
            $newItem[] = '{pour ' . $value . '}'; // The original placeholder
            $newPlaceHolders[] = $newItem;
        }
        foreach ($newPlaceHolders as $pph) {
            if (count($pph) != 3) {
                $results->addToError("Expecting 3 values, got: " . implode(', ', $pph));
                continue;
            }
            $pphKeys = array_keys($pph);
            $nameToken = $pph[$pphKeys[0]];
            if ($nameToken[0] != '$') {
                $results->addToError('Expecting first character of ' . $nameToken . ' to be \'$\'' );
                continue;
            }
            $pphVarName = substr($nameToken, 1); // The name without the token's first character, which was a $
            if (!in_array($pphVarName, $dataNames)) {
                error_log( '(Variable name ' . $pphVarName . ' was not in data names ' . implode(' | ',$dataNames) . ')');
                continue;
            }
            $newMoldName = $pph[$pphKeys[1]];
            $newMoldName = substr($newMoldName, 1, strlen($newMoldName)-2); // strip off the beginning and ending apostrophe quotes ''
            $newMoldName = dirname($moldName) . DIRECTORY_SEPARATOR . $newMoldName;
                    
            $originalPlaceHolder = $pph[$pphKeys[2]];

            /*if (Utils::isDict($liquid[$pphVarName])) {
                $recursiveResults = self::pour($newMoldName, $liquid[$pphVarName], $useHtmlSpecialChars, $depth+1);
                $results->addToError( $recursiveResults->getError());
                $newString = str_replace($originalPlaceHolder, $recursiveResults->getInfo(), $newString);
            } else*/
            if (!is_array($liquid[$pphVarName])) {
                $results->addToError('Expecting ' . $pphVarName . ' value to be an array. ');
                continue;
            }
            $accum = '';
            foreach ($liquid[$pphVarName] as $subValues) {
                // **** RECURSION ****
                $thisRecord = self::pour($newMoldName, $subValues, $useHtmlSpecialChars, $depth+1);
                $accum = $accum . $thisRecord->getInfo();
            }
            $newString = str_replace($originalPlaceHolder, $accum, $newString);
        }
        $results->setInfo($newString);
        return $results;
    }


}