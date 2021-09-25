<?php
declare(strict_types=1);
namespace LM\Foundry;
class Cast
{
    static function pour(string $moldName, array $liquid, bool $useHtmlSpecialChars=true ) : Results
    {
        $results = new Results();

        $guides = self::getGuides($moldName, $liquid, $useHtmlSpecialChars);
		if (strlen( $guides['error'] ) > 0 ) {
			$results->setError( $guides['error'] );
			return $results;
		}

        if (Utils::isDict($liquid)) {
        	return self::pourOne($liquid, $guides);
        }

        for ($num=1; $num <= count($liquid); $num++) {
        	$one = $liquid[$num-1];

        	if (!Utils::isDict($one)) {
        	    $results->addToError("Pour(): Expecting elements to be associative arrays. {$moldName}, #{$num}");

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
    }

    private static function getGuides($moldName, $liquid, $useHtmlSpecialChars) : array 
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
    }


	/* Pour the liquid (the data) into the mold. Guides contains the mold and the hollows to be filled.
	 *
	 * Assumes the caller code has already verified that $liquid is an array where all keys are strings.
	 */
	private static function pourOne(array $liquid, array $guides) : Results
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
        	/* NOTE! '&$' The following foreach loop changes the array's values in-place, i.e. by reference. */
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
	}

	private static function validateLiquid(array $liquid, array $guides) : Results
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
	}

}