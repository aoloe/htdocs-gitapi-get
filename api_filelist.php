<?php

/**
 * Output a directory listing compatible with the result of the Github API for the requests of the type:
 *
 * https://api.github.com/repos/$user/$repository/git/trees/master?recursive=1
 *
 * The request is of the type:
 *
 * http://test.com/api_filelist.php?hash=abcd
 *
 * The result is of type text/json
 *
 * - This is useful to test the script without quering Github.
 * - This version directly queries the local filesystem.
 * - The hash is a 40 hex digit sha1 hash built as sha1("blob " + filesize + "\0" + data).
 * - The available repositores are defined in the config.json file and can be queried with the hash parameter
 *   (GET or POST).
 * - It tries to recognize and respect .gitignore files.
 */

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

function sha1_git($filename) {
    // debug('filesize', filesize($filename));
    // debug('file_get_contents', file_get_contents($filename));
    return sha1("blob ".filesize($filename)."\0".file_get_contents($filename));
}
// debug('sha1_file', sha1_file('/home/ale/notes.txt'));
// debug('sha1_git', sha1_git('/home/ale/notes.txt'));

/**
 * @param array(<string>)/string $pattern if an array it's true if one of the strings matches.
 * @return bool
 *
 * inspired by DaveRandom in http://stackoverflow.com/questions/13913796/php-glob-style-matching
 */
function path_matches($path, $pattern, $ignoreCase = false) {

    if (is_array($pattern)) {
        foreach ($pattern as $item) {
            if (path_matches($path, $item, $ignoreCase)) {
                return true;
            }
        }
        return false;
    } else {
        $expr = preg_replace_callback(
            '/[\\\\^$.[\\]|()?*+{}\\-\\/]/',
            function($matches) {
                switch ($matches[0]) {
                    case '*':
                        return '.*';
                    case '?':
                        return '.';
                    default:
                        return '\\'.$matches[0];
                }
            },
            $pattern
        );

        $expr = '/'.$expr.'/';
        if ($ignoreCase) {
            $expr .= 'i';
        }

        return (bool) preg_match($expr, $path);
    }

} // path_matches()


function get_gitapiget_filelist_tree($path, $path_base = null, $gitignore = null) {
    $result = array();
    // debug('path', $path);
    if (is_null($path_base)) {
        $path_base = $path;
    }
    $path_base_n = strlen($path_base) + 1;
    if (file_exists($path.'/.gitignore')) {
        $gitignore = array_filter(explode("\n", file_get_contents($path.'/.gitignore')));
        // debug('gitignore', $gitignore);
    }
    if ($handle = opendir($path)) {
        while (false !== ($filename = readdir($handle))) {
            // TODO: we may want to consider the .gitignore at the root
                // debug('filename', $filename);
                $path_item = $path.'/'.$filename;
                if (is_dir($path_item)) {
                    if ($filename != "." && $filename != ".." && $filename != ".git") {
                        $result[] = array(
                            'type' => 'tree',
                            'sha' => '',
                            'path' => substr($path_item, $path_base_n),
                            'url' => '???',
                        );
                        if (is_readable($path_item)) {
                            $result = array_merge($result, get_gitapiget_filelist_tree($path_item, $path_base, $gitignore));
                        }
                    }
                } elseif (is_file($path_item) && is_readable($path_item)) {
                    $path_local = substr($path_item, $path_base_n);
                    // debug('path_local', $path_local);
                    // debug('path_matches', path_matches($path_local, $gitignore));
                    if (!isset($gitignore) || !path_matches($path_local, $gitignore)) {
                        $result[] = array(
                            'type' => 'blob',
                            'sha' => sha1_git($path_item),
                            'path' => $path_local,
                            'url' => '???',
                        );
                    }
                }
            }
    }
    return $result;
} // get_gitapiget_filelist_tree()

/**
 * Given an hash, checks for the related path in the config file and returns it.
 * 
 * Use this function if you want to serve different paths depending on an id  passed
 * as an HTTP parameter: avoid using the HTTP parameter as parts of the paths!
 */
function get_gitapiget_filelist_path($hash = null) {
    $result = null;

    if (!defined('GITAPIGET_FILELIST_CONFIG_FILE')) {
        define('GITAPIGET_FILELIST_CONFIG_FILE', 'config.json');
    }

    if (file_exists(GITAPIGET_FILELIST_CONFIG_FILE)) {
        $config = json_decode(file_get_contents(GITAPIGET_FILELIST_CONFIG_FILE), true);
        // debug('config', $config);
    } else {
        debug('could not find the config file', GITAPIGET_FILELIST_CONFIG_FILE);
    }

    if (is_null($hash)) {
        $hash = array_key_exists('hash', $_REQUEST) ? $_REQUEST['hash'] : null;
    }

    if (isset($hash) && array_key_exists($hash, $config['repository']) && array_key_exists('path', $config['repository'][$hash])) {
        $result = rtrim($config['repository'][$hash]['path'], '/');
    }

    // debug('result', $result);
    return $result;
}

function render_gitapiget_filelist($path) {
    $result = array (
        'sha' => 'gitapi-get',
        'url' => 'http'.(strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? '' : 's').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
        'tree' => array (
            /*
            array(
            'type' => 'blob' | 'tree',
            'sha' => '????', // 40 hex digit sha1 hash -> git hash-object filename; not needed for dirs
            'path' => 'full/path/to/file.ext' |  'full/path/to/directory', 
            'url' => '???',
            )
            */
        ),
    );
    // debug('path', $path);
    if (isset($path) && ($path != '') && file_exists($path)) {
        $result['tree'] = get_gitapiget_filelist_tree($path);
    }

    if ((ob_get_length() == 0) && !headers_sent()) {
        header('Content-type: text/json');
    } else {
        ob_flush();
        // flush();
        $file_wrote = '';
        $line_wrote = '';
        headers_sent($file_wrote, $line_wrote); // FIXME: returns the line where ob_flush is, not the output one
        debug('haeders already sent', $file_wrote.' ['.$line_wrote.']');
    }
    echo(json_encode($result));
} // render_gitapiget_filelist()

// phpinfo();



/*
 * sample for the return values from the github API. remove it as soon as "my" API is somehow stable
{
    "sha":"master",
    "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/trees\/master",
    "tree":
        [
            {
                "mode":"100644",
                "type":"blob",
                "sha":"a01ee289f9a3c65287845c5138783d4f3b24a443",
                "path":".gitignore",
                "size":7,
                "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/blobs\/a01ee289f9a3c65287845c5138783d4f3b24a443"
            },
            {
                "mode":"100644",
                "type":"blob",
                "sha":"12929114085b3bb86e5e48d9a984f28fd382c129",
                "path":"LICENSE",
                "size":26,
                "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/blobs\/12929114085b3bb86e5e48d9a984f28fd382c129"
            },
            {
                "mode":"100644",
                "type":"blob",
                "sha":"cc0890869b4fa4d58d13f31aee2be66896b31b30",
                "path":"NOTES.md",
                "size":189,
                "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/blobs\/cc0890869b4fa4d58d13f31aee2be66896b31b30"
                },
                {
                    "mode":"100644",
                    "type":"blob",
                    "sha":"c8ea6f85fd7b87e6344735fa7e523a7b04d5dfa8",
                    "path":"README.md",
                    "size":1672,
                    "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/blobs\/c8ea6f85fd7b87e6344735fa7e523a7b04d5dfa8"
                },
                {
                    "mode":"100644",
                    "type":"blob",
                    "sha":"4c5baf098c77fd6fc57ecae9de3c9d6054e720f9",
                    "path":"README_charte_graphique.md",
                    "size":1129,
                    "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/blobs\/4c5baf098c77fd6fc57ecae9de3c9d6054e720f9"
                },
                {
                    "mode":"100644",
                    "type":"blob",
                    "sha":"3e3ee4a2d56bef485b21d1c92adc67e916cf8fc5",
                    "path":"book.yaml",
                    "size":6897,
                    "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/blobs\/3e3ee4a2d56bef485b21d1c92adc67e916cf8fc5"
                },
                {
                    "mode":"040000",
                    "type":"tree",
                    "sha":"469443649ab3fa7399c9e41d1b1db7b5d104bc07",
                    "path":"content",
                    "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/trees\/469443649ab3fa7399c9e41d1b1db7b5d104bc07"
                },
                {
                    "mode":"040000",
                    "type":"tree",
                    "sha":"609f113ff0d1f655336729d110b62d83201a5894",
                    "path":"content\/concepts-file_formats",
                    "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/trees\/609f113ff0d1f655336729d110b62d83201a5894"
                },
                {
                    "mode":"100644",
                    "type":"blob",
                    "sha":"dc0230c3789d7c5fa8b4a629fa30385c2c366084",
                    "path":"content\/concepts-file_formats\/concepts-file_formats-fr.md",
                    "size":9
                    8,
                    "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/blobs\/dc0230c3789d7c5fa8b4a629fa30385c2c366084"
                },
                {
                    "mode":"040000",
                    "type":"tree",
                    "sha":"a6ea1d12bc23e4ef801e6ddc89dc74d31e7c698e",
                    "path":"content\/gimp-automatic_enhancements",
                    "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/trees\/a6ea1d12bc23e4ef801e6ddc89dc74d31e7c698e"
                },
                {
                    "mode":"100644",
                    "type":"blob",
                    "sha":"994859b25355b1140909253f02e93d6558eb4975",
                    "path":"content\/gimp-automatic_enhancements\/gimp-automatic_enhancements-fr.md",
                    "size":1326,
"url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/blobs\/994859b25355b1140909253f02e93d6558eb4975"
                },
                {
                    "mode":"040000",
                    "type":"tree",
                    "sha":"1c9624cabf78ec5cc4e825feec0017a800b9f834",
                    "path":"content\/gimp-brightness_and_contrast",
                    "url":"https:\/\/api.github.com\/repos\/aoloe\/libregraphics-manual-libregraphics_for_ONGs\/git\/trees\/1c9624cabf78ec5cc4e825feec0017a800b9f834"
                },
            ]
}
*/
