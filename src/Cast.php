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

        // x24 = $   x20 = a space   . = any character   x27 = single quote (apostrophe)
        $pourPattern = '#{(pour' . '\x20+' .'\x24' . $pat .'*[\x20][\x20]*' . '[\x27][\x20-\x7e]+[\x27]' . '.*)}#';
        $pourPlaceHolders = Utils::getPlaceholders($moldString, $pourPattern);
        $pourPlaceHolders = Utils::getMatches($pourPlaceHolders, '{pour ', '}' );
        $newPlaceHolders = [];
        foreach ($pourPlaceHolders as $value) {
            $newItem = array_filter(explode(' ', trim($value, ' ') ) );
            $newItem[] = '{pour ' . $value . '}';
            $newPlaceHolders[] = $newItem; 
        }

        foreach ($newPlaceHolders as $pph) {
            if (count($pph) != 3) {
                $results->addToError("Expecting 3 values, got: " . implode(', ', $pph));
            } else {
                $pphKeys = array_keys($pph);
                $nameToken = $pph[$pphKeys[0]];
                $newMoldName = $pph[$pphKeys[1]];
                $originalPlaceHolder = $pph[$pphKeys[2]];
                $pphVarName = substr($nameToken, 1); // The name without the token's first character, which is supposed to be a $
                $newMoldName = dirname($moldName) . DIRECTORY_SEPARATOR . substr($newMoldName, 1, strlen($newMoldName)-2);
                if ($nameToken[0] != '$') {
                    $results->addToError('Expecting first character of ' . $nameToken . ' to be \'$\'' );
                } else if (in_array($pphVarName, $dataNames)) {
                    if ( Utils::isDict($liquid[$pphVarName])) {
                        $recursiveResults = self::pour($newMoldName, $liquid[$pphVarName], $useHtmlSpecialChars, $depth+1);
                        $results->addToError( $recursiveResults->getError());
                        $newString = str_replace($originalPlaceHolder, $recursiveResults->getInfo(), $newString);
                    } else {
                        $num = 1;
                        $accumulated = '';
                        foreach ($liquid[$pphVarName] as $dict) {
                            if (Utils::isDict($dict)) {
                                $recursiveResults = self::pour($newMoldName, $dict, $useHtmlSpecialChars, $depth+1);
                                $results->addToError( $recursiveResults->getError());
                                $newString = str_replace($originalPlaceHolder, $recursiveResults->getInfo(), $newString);
                                if ($num < count($liquid[$pphVarName])) {
                                    // Something about this is not quite right, but I don't know what I'm doing wrong.
                                    // Look at the html test output data
                                    $newString .= $originalPlaceHolder;
                                }
                                $num++;
                            }
                        }
                    }
                } else {
                    $results->addToError( "Name " . $pphVarName .  " not in data array. " . implode(' | ',$dataNames).' ' );
                }
            }
        }
        $results->setInfo($newString);
        return $results;
    }

    /*private static function getGuides($moldName, $liquid, $useHtmlSpecialChars) : array 
    {
    	$guides = array();

    	$guides['num'] = -1;
    	$guides['error'] = '';
    	$guides['moldName'] = $moldName;
    	$guides['useHtmlSpecialChars'] = $useHtmlSpecialChars;
    	$guides['moldString'] = '';
    	$guides['placeHolders'] = array();

		$results = Utils::getFileContents($moldName);
        $guides['moldString'] = strval( $results->getInfo() );

        if ($results->hasError() ) {
            $guides['error'] = "Error reading file {$moldName}. " . $results->getError();
            return $guides;
        }
        if ($guides['moldString'] == '') {
			$guides['error'] = "Error getting contents of $moldName.";
        } else {
            $guides['placeHolders'] = Utils::getPlaceholders( $guides['moldString'] );
        }
        return $guides;
    }*/


	/* Pour the liquid (the data) into the mold. Guides contains the mold and the hollows to be filled.
	 *
	 * Assumes the caller code has already verified that $liquid is an array where all keys are strings.
	 */
	/*private static function pourOne(array $liquid, array $guides) : Results
	{
        $results = new Results;
        if (strlen( $guides['error'] ) > 0 ) {
            $results->setError($guides['error']);
            return $results;
        }
        $validated = self::validateLiquid( $liquid, $guides );
        if ($validated->hasError()) {
             
            $results->addToError( 'Invalid pairs. ' . $validated->getError() . ' ' 
                . http_build_query($liquid, arg_separator:', ') .PHP_EOL );

        } else {
        	/* NOTE! '&$' The following foreach loop changes the array's values in-place, i.e. by reference. * /
            foreach ( $liquid as $name => &$value ) {
                if ( $guides['useHtmlSpecialChars'] ) { $value = htmlspecialchars ( strval( $value ) ); }
                else                                  { $value = strval( $value ); }
            }
            $liquid['num'] = $guides['num'];
            $replaced = Utils::replaceVariablesInMold( $guides['moldString'], $liquid );
            if (is_null($replaced)) {
                $results->addToError('replaceVariablesInMold failed. Mold: ' . $guides['moldName'] . ' #' . $guides['num'] );

            } else {
                $results->addToInfo( $replaced );
            }
        }
        return $results;
	}*/

	/*private static function validateLiquid(array $liquid, array $guides) : Results
    {
	    $results = new Results;
	    $varNames = []; // Our list of variable names, a.k.a. the keys of $liquid dict.
	    foreach ($liquid as $varName => $value ) {
	        $varNames[] = $varName;
	        if ( !Utils::isLegalVarName($varName) ) {
	            if ( $results->hasError() ) { $results->addToError("\n"); } // Add newline delimiter if needed
	            $results->addToError("Badly formed variable name: '{$varName}'" );
	        }
	    }

        $placeHolders = $guides['placeHolders'];
		foreach ($placeHolders as $placeHolder) {
		    if ( $placeHolder != 'num' && !in_array($placeHolder, $varNames) ) {
		        if ( $results->hasError() ) { $results->addToError("\n"); } // Add newline delimiter if needed
		        $results->addToError("Missing value for '{$placeHolder}'.");
		    }
		}
	    return $results;
	}*/

}