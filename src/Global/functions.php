<?php

if (!function_exists('mb_ucfirst')) {
    /**
     * Make a string's first character uppercase
     * 
     * @param string $str
     * @param string $encoding
     * @return string
     */
    function mb_ucfirst(string $str, string $encoding = 'UTF-8'): string
    {
        return mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) . mb_substr($str, 1, null, $encoding);
    }
}

if (!function_exists('mb_strtotitle')) {
    /**
     * Make a string's first character of all words uppercase
     * 
     * @param string $str
     * @param string $encoding
     * @return string
     */
    function mb_strtotitle(string $str, string $encoding = 'UTF-8'): string
    {
        return mb_convert_case($str, MB_CASE_TITLE, $encoding);
    }
}
