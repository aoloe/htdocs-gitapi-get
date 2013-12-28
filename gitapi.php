<?php
/**
 * Implement the main gitapi-get functions.
 */

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

define('GITAPIGET_GITAPI_LIB', true);

function get_gitapi_github_ratelimit_url() {
    return 'https://api.github.com/rate_limit';
}

function get_gitapi_github_filelist_url($user, $repository, $branch = null) {
    return strtr(
        'https://api.github.com/repos/$user/$repository/git/trees/$branch?recursive=1',
        array(
            '$user' => $user,
            '$repository' => $repository,
            '$branch' => isset($branch) ? $branch : 'master',
        )
    );
}

function get_gitapi_github_raw_url($user, $repository, $branch = null) {
    return strtr(
        'https://raw.github.com/$user/$repository/$branch/',
        array(
            '$user' => $user,
            '$repository' => $repository,
            '$branch' => isset($branch) ? $branch : 'master',
        )
    );
}

function get_gitapi_self_ratelimit_url($hash = null) {
    // debug('_SERVER', $_SERVER);
    // TODO: use a constant for the api_filelist file name
    $result = 'http'.(strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? '' : 's').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/api_ratelimit.php'.(isset($hash) ? '?hash='.$hash : '');
    // debug('result', $result);
    return $result;
}

function get_gitapi_self_filelist_url($hash) {
    // debug('_SERVER', $_SERVER);
    // TODO: use a constant for the api_filelist file name
    $result = 'http'.(strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? '' : 's').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/api_filelist.php?hash='.$hash;
    // debug('result', $result);
    return $result;
}

function get_gitapi_self_raw_url($hash = null) {
    $result = 'http'.(strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? '' : 's').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/api_fileraw.php'.(isset($hash) ? '?hash='.$hash : '');
    // debug('result', $result);
    return $result;
}

/**
 * @param string $url
 * @param string $cache_id
 * @param array $cache
 */
function get_gitapi_list($url) {
    $result = array();
    // debug('url', $url);
    $raw = get_gitapi_raw($url);
    // debug('raw', $raw);
    $result = json_decode($raw, true);
    return $result;
} // get_gitapi_list()

function get_gitapi_compared_list($list, $cache) {
    $result = array();
    foreach ($list['tree'] as $item) {
        if ($item['type'] == 'blob') {
            if (array_key_exists($item['path'], $cache)) {
                $item['status'] = (($cache[$item['path']]['sha'] == $item['sha']) ? 'keep' : 'update');
                unset($cache[$item['path']]);
            } else {
                $item['status'] = 'new';
            }
        } else {
            unset($cache[$item['path']]); // remove the directories
            unset($item);
        }
        if (isset($item)) {
            // debug('item', $item);
            $result[$item['path']] = $item;
        }
    }
    foreach ($cache as $item) {
        if ($item['status'] != 'delete') {
            // debug('item', $item);
            $item['status'] = 'delete';
            $result[$item['path']] = $item;
        }
    }
    ksort($result);
    // debug('result', $result);
    return $result;
}

function get_gitapi_tree($url, $cache = null) {
    $result = array();
    $list = get_gitapi_list($url, $cache);
    $list = get_gitapi_compared_list($list, $cache);
    // debug('list', $list);
    foreach ($list as $item) {
        // debug('path', $item['path']);
        if ($item['type'] == 'tree') {
            // $item['tree'] = array();
            set_gitapi_array_path($result, $item['path'], $item);
        } else {
            set_gitapi_array_path($result, $item['path'], $item);
        }
    }
    // debug('result', $result);
    return $result;
} // get_git_api_tree()

/**
 * make an http call to the given url and return the result as text
 */
function get_gitapi_raw($url) {
    $result = '';
    debug('url', $url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'aoloe/htdos-gitapi-get');
    // curl_setopt($ch, CURLOPT_HEADER, true);
    // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $result = curl_exec($ch);
    $info = curl_getinfo($ch);
    // debug('info', $info);
    if (($info['http_code'] == '404') || ($info['http_code'] == '403')) {
        debug('get_gitapi_raw: invalid url', $url);
        $result = '';
    }
    curl_close($ch);
    // debug('result', $result);
    return $result;
} // get_gitapi_raw()

function ensure_gitapi_cache($path_cache) {

}

/**
 * Update the cache based on the list of files returned by the API and already compared with the list
 * in the cache
 * @return list of the files modified/deleted.
 * Updates the cache:
 * - add new files
 * - update modified files
 * - remove deleted files
 */
function update_gitapi_cache($list, $path_cache, $url_raw, $use_get) {
    $result = array();
    foreach ($list as $key => $value) {
        // debug('value', $value);
        if ($value['type'] == 'blob') { // it's a file
            // debug('value[path]', $value['path']);
            switch ($value['status']) {
                case 'new' :
                case 'update' :
                    put_gitapi_cache($value['path'], get_gitapi_raw($url_raw.($use_get ? '?file=' : '/').$value['path']), $path_cache);
                    $result[$value['path']] = $value['status'];
                break;
                case 'delete' :
                    delete_gitapi_cache($value['path'], $path_cache);
                    $result[$value['path']] = $value['status'];
                break;
            }
        }
    }
    return $result;
} // update_gitapi_cache

/**
 * @deprecated: tree based cache management... 
 */
function update_gitapi_tree_cache($tree, $path_cache, $url_raw, &$action) {
    foreach ($tree['tree'] as $key => $value) {
        if (!array_key_exists('type', $value) || ($value['type'] == 'tree')) { // it's a directory
            // debug('value', $value);
            if (array_key_exists('tree', $value)) {
                update_gitapi_cache($value, $path_cache, $url_raw, $action);
            }
        } else { // it's a file
            // debug('value[path]', $value['path']);
            switch ($value['status']) {
                case 'new' :
                case 'update' :
                    put_gitapi_cache($value['path'], get_gitapi_raw($url_raw), $path_cache);
                break;
                case 'delete' :
                    delete_gitapi_cache($value['path'], $path_cache);
                break;
            }
            $action[$value['path']] = $value['status'];
        }
    }
} // update_gitapi_cache
