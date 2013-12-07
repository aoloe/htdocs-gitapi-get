<?php
/**
 * Return the raw version of a file in a way that is compatible with Github raw requests defined as:
 *
 * https://raw.github.com/$user/$repository/master/$path/$to/$file
 *
 * The request is of the type:
 *
 * http://test.com/api_raw.php?hash=abcd&file=relative/path/to/file.ext
 *
 * The result is of type text/text (TODO: or depends on the exitension?)
 *
 * - This is useful to test the script without quering Github
 */

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}
