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

function debug($label, $value) {
    echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
}

if (!defined('GITAPIGET_FILELIST_CONFIG_FILE')) {
    define('GITAPIGET_FILELIST_CONFIG_FILE', 'config.json');
}

if (file_exists(GITAPIGET_FILELIST_CONFIG_FILE)) {
    $config = json_decode(file_get_contents(GITAPIGET_FILELIST_CONFIG_FILE), true);
    // debug('config', $config);
} else {
    debug('could not find the config file', GITAPIGET_FILELIST_CONFIG_FILE);
}

$hash = array_key_exists('hash', $_REQUEST) ? $_REQUEST['hash'] : '';

$path_repository = null;

if (array_key_exists($hash, $config['repository']) && array_key_exists('path', $config['repository'][$hash])) {
    $path_repository = rtrim($config['repository'][$hash]['path'], '/');
}

$filename = array_key_exists('file', $_REQUEST) ? $_REQUEST['file'] : '';

// TODO: sanitize filename in order to avoid tweaking the file parameter to get files outside of the repository
// the hard part will be to allow symlinks!
// explode path and add it manually by ignoring empty strings and '..'? any way to get all .. that can
// be recognized by the filesystem?

$path_file = $path_repository.'/'.$filename;

if (file_exists($path_file) && is_file($path_file)) {
    if ((ob_get_length() == 0) && !headers_sent()) {
        // TODO: check for which extensions github returns different content types
        header('Content-type: text/plain'); // .md .yaml
        echo(file_get_contents($path_file));
    } else {
        ob_flush();
        // flush();
        $file_wrote = '';
        $line_wrote = '';
        headers_sent($file_wrote, $line_wrote); // FIXME: returns the line where ob_flush is, not the output one
        debug('haeders already sent', $file_wrote.' ['.$line_wrote.']');
    }
} else {
    debug('file not found', $path_file);
}
