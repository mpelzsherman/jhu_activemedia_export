<?php
class StringUtils {

    public static function windowsFilenameSafeString($string) {
        $string = preg_replace('/[^\w\-~_\.]+/u', '_', $string);
        return strtolower(preg_replace('/__+/u', '_', $string));
    }

}