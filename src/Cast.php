<?php
declare(strict_types=1);
namespace LM\Foundry;
class Cast
{
    static function pour(string $moldFileName, mixed $liquid, bool $useHtmlSpecialChars=true, int $depth=0 ) : string|array
    {
        // TODO: Change $moldFileName to an array or object or set. Use $depth to help determine which of the molds to use...
        // Somehow figure out, when recursive call is made, which mold file to fill.
        
        $results = new Results();
        // Take care of base cases:
        if (is_bool($liquid)) {
            return $liquid ? 'true' : 'false';
        }
        elseif (is_scalar($liquid) || is_null($liquid)) { // scalar is one of int, float, string or bool
            return strval($liquid);
        }
        elseif (is_callable($liquid) ) {
            return 'pour(): Unexpected type: callable';
        }
        elseif (is_resource($liquid) ) {
            return 'pour(): Unexpected type: resource';
        }

        // ******************************************************************************************
        // * At some point, explore iterator_apply() - call function for every element in iterator
        // ******************************************************************************************

        // After taking care of base cases, what is left? : array, object, or iterable
        if (is_object($liquid)) {
            $name_val_pairs = get_object_vars($liquid);
        } else {
            $name_val_pairs = $liquid;
        }

        echo "Pour(), depth: {$depth}. Type: " . gettype($liquid) . ". Count(name_val_pairs): " . strval(count($name_val_pairs)) . "\n";

        // Now, use $name_val_pairs instead of $liquid.

        // getGuides returns an array, not Results.
        $guides = self::getGuides($moldFileName, $useHtmlSpecialChars);
		if (strlen( $guides['error'] ) > 0 ) {
			$results->setError( $guides['error'] );
			return $results;
		}

        $origMold = $guides['moldString'];
        $curMold = $origMold;
        $arr = array();
        foreach ($name_val_pairs as $pair_name => $pair_val) {
            $name = strval($pair_name);
            if (is_scalar($pair_val) || is_null($pair_val)) {
                $str = $useHtmlSpecialChars? htmlspecialchars( strval($pair_val) ) : strval($pair_val);
                $curMold = str_replace('{$'.$name.'}', $str, $curMold );
            } else {
                // RECURSION:
                $arr[$pair_name] = Cast::pour($moldFileName, $pair_val, $useHtmlSpecialChars, $depth+1);
            }
        }
        if (count($arr) == 0)
            return $curMold;
        elseif ($curMold == $origMold)
            return $arr;
        else
            return [$curMold, $arr];
    }
        /*
        if (Utils::isDict($liquid)) {
        	return self::pourOne($liquid, $guides);
        }

        for ($num=1; $num <= count($liquid); $num++) {
        	$one = $liquid[$num-1];

        	if (!Utils::isDict($one)) {
        	    $results->addToError("Pour(): Expecting elements to be associative arrays. {$moldFileName}, #{$num}");

        	} else {
        		$guides['num'] = $num;
                $oneResults = self::pourOne($one, $guides);

                if ($oneResults->hasError()) {
                    $results->addToError($oneResults->getError());

                } else {
                    $results->addToInfo($oneResults->getInfo());
                }
            }
        }
        return $results;
    }*/

    private static function getGuides($moldFileName, $useHtmlSpecialChars) : array 
    {
    	$guides = array();

    	$guides['error'] = '';
    	//$guides['moldFileName'] = $moldFileName;
    	//$guides['useHtmlSpecialChars'] = $useHtmlSpecialChars;
    	$guides['moldString'] = '';
    	//$guides['placeHolders'] = array();

		$results = Utils::getFileContents($moldFileName);
        $guides['moldString'] = strval( $results->getInfo() );

        if ($results->hasError() ) {
            $guides['error'] = "Error reading file {$moldFileName}. " . $results->getError();
            return $guides;
        }
        if ($guides['moldString'] == '') {
			$guides['error'] = "Error getting contents of $moldFileName.";
        } /*else {
            $guides['placeHolders'] = Utils::getPlaceholders( $guides['moldString'] );
        }*/
        return $guides;
    }


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