<?php

/**
 */

define('GITAPIGET_FILERAW_LIB', true);

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
function get_gitapi_fileraw($path_file) {
    $result = null;
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
        echo($result);
    }
} // render_apiget_fileraw()
