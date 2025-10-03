<?php
namespace Src\Utils;
class Validator { public static function isText($t){ return is_string($t)&&strlen($t)>0; } }
