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

/*
$hash = array_key_exists('hash', $_REQUEST) ? $_REQUEST['hash'] : '';

$path_repository = null;

if (array_key_exists($hash, $config['repository']) && array_key_exists('path', $config['repository'][$hash])) {
    $path_repository = rtrim($config['repository'][$hash]['path'], '/');
}
*/
function get_gitapi_fileraw($filename) {
    $result = null;
    // $path_file = $path_repository.'/'.$filename;
    $path_file = $filename;

    if (file_exists($path_file) && is_file($path_file)) {
        $result = file_get_contents($path_file);
    } else {
        debug('file not found', $path_file);
    }
    return $result;
} // get_apiget_fileraw()

function render_gitapi_fileraw($filename) {
    if (isset($filename)) {
        $result = get_gitapi_fileraw($filename);
        if (isset($result)) {
            if ((ob_get_length() == 0) && !headers_sent()) {
                // TODO: check for which extensions github returns different content types
                header('Content-type: text/plain'); // .md .yaml
            } else {
                ob_flush();
                // flush();
                $file_wrote = '';
                $line_wrote = '';
                headers_sent($file_wrote, $line_wrote); // TODO: returns the line where ob_flush is, not the output one
                debug('haeders already sent', $file_wrote.' ['.$line_wrote.']');
            }
        }
    }
} // render_apiget_fileraw()
