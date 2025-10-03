<?php
namespace Src\Utils;
class Helper { public static function log($txt){ error_log($txt."\n",3,__DIR__.'/../../storage/logs/error.log'); } }
