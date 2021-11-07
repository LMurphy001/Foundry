<?php
spl_autoload_register(
    function (string $name) { 
        /* Get the Foundry source code from the 'src' folder. */
        $nameSpace = 'LM\FOUNDRY\\';
        if ( str_starts_with(strtoupper($name), $nameSpace) ) {
            $fileName = __DIR__.DIRECTORY_SEPARATOR. 
                '..' .DIRECTORY_SEPARATOR. /* Where did we put LM\Foundry src folder relative to this code? */
                'src' .DIRECTORY_SEPARATOR.
                substr($name, strlen($nameSpace) ).'.php';
            if (file_exists( $fileName)) { require_once $fileName; } else{error_log( "\n===> NOT FOUND: '{$fileName}'\n"); } } } );
