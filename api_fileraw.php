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
 * The result is of type text/text (TODO: or depends on the extension?)
 *
 * - This is useful to test the script without quering Github
 */

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

function render_gitapiget_fileraw($path, $filename = null) {
    $result = null;
    if (is_null($filename)) {
        if (array_key_exists('file', $_REQUEST)) {
            $filename = $_REQUEST['file'];
        }
    }
    // TODO: move this to the caller! ... or to the parameters
    if (isset($filename)) {
        if (defined('GITAPIGET_API_FILERAW_URLENCODE_PATH') && GITAPIGET_API_FILERAW_URLENCODE_PATH) {
            $filename = urlencode($filename);
        }

        $result = get_gitapiget_fileraw($path.$filename);
    }

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
        echo($result);
    }

}

function get_gitapiget_fileraw($path_file) {
    $result = null;
    if (file_exists($path_file) && is_file($path_file)) {
        $result = file_get_contents($path_file);
    } else {
        debug('file not found', $path_file);
    }
    return $result;
}
