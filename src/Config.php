<?php
declare(strict_types=1);
namespace LM\Foundry;

class Config
{
    static public function getMoldDir() : string {
        $configFile = __DIR__ .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR. 'config.ini';
        $parsed = parse_ini_file($configFile);
        $mold_dir = $parsed['MOLD_DIR'];
        $mold_dir = str_replace('/', DIRECTORY_SEPARATOR, $mold_dir);
        if (substr($mold_dir, -1) != DIRECTORY_SEPARATOR) {
            $mold_dir = $mold_dir . DIRECTORY_SEPARATOR;
        }
        $mold_dir = __DIR__ .DIRECTORY_SEPARATOR. $mold_dir;
        return $mold_dir;
    }
}