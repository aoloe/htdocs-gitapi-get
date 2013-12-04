<?php
/**
 * write and read cache files
 */

define('GITAPIGET_CACHE_LIB', true);

if (!defined('GITAPIGET_CACHE_PATH')) {
    define('GITAPIGET_CACHE_PATH', 'cache/');
}

function ensure_directory_writable($path, $base_path = '') {
    $result = false;
    if ($base_path != '') {
        $base_path = rtrim($base_path, '/').'/';
        $path = trim(substr($path, count($base_path) -1), '/');
    }
    if (file_exists($base_path.$path)) {
        $result = is_dir($base_path.$path) && is_writable($base_path.$path);
    } else {
        $result = true;
        $path_item = $base_path;
        foreach (explode('/', $path) as $item) {
            $path_item .= $item.'/';
            if (!file_exists($path_item)) {
                $result = mkdir($path_item);
                // if (!$result) debug('path_item', $path_item);
            } else {
                $result = is_dir($path_item);
            }
            if (!$result) {
                break;
            }
        }
        $result &= is_writable($base_path.$path);
    }
    return $result;
}

/**
 * check if the file is writable and if not, check that the directories leading to it do exist or can
 * be created
 * @param string $path the path, inclusive the file name, separated by /.
 * it must be a relative path starting from the current directory or from $base_path when defined.
 * @param string $path the part of the path where it should not create directories.
 */
function ensure_file_writable($path, $base_path = '') {
    $result = false;
    if ($base_path != '') {
        $base_path = rtrim($base_path, '/').'/';
        $path = trim(substr($path, count($base_path) - 1), '/');
    }
    if (file_exists($base_path.$path)) {
        $result = is_file($base_path.$path) && is_writable($base_path.$path);
    } else {
        $result = ensure_directory_writable(dirname($path), $base_path);
    }
    return $result;
} // ensure_file_writable

function put_cache($path, $content, $manual_id = null) {
    $result = false;
    $path_cache = (isset($manual_id) ? $manual_id.'/' : '').$path;
    if (ensure_file_writable($path_cache, MANUAL_CACHE_PATH)) {
        file_put_contents(MANUAL_CACHE_PATH.$path_cache, $content);
    }
    return $result;
} // put_cache()

function get_cache($path, $content, $manual_id = null) {
    $result = false;
    $path_cache = (isset($manual_id) ? $manual_id.'/' : '').$path;
    if (ensure_file_writable($path_cache, MANUAL_CACHE_PATH)) {
        file_put_contents(MANUAL_CACHE_PATH.$path_cache, $content);
    }
    return $result;
} // put_cache()

function get_gitapi_cache($url, $base_path = null) {
    $result = '';
    if (is_null($base_path)) {
        $base_path = GITAPIGET_CACHE_PATH;
    }
    $file_name = $base_path.'/'.$url;
    if (file_exists($file_name)) {
        $result = file_get_contents($file_name);
    }
    return $result;
} // get_gitapi_cache()

function put_gitapi_cache($url, $content, $base_path = null) {
    if (is_null($base_path)) {
        $base_path = GITAPIGET_CACHE_PATH;
    }
    $file_name = $base_path.'/'.$url;
    // TODO: if file writable
    $result = file_put_contents($file_name, $content);
} // put_gitapi_cache()
