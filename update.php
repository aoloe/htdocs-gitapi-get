<?php
/**
 * use an API compatible to the Github one to get the list of the files in the
 * git repository and, through their hashes, find out which ones are to be updated in
 * the local cache.
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

include_once('treepath.php');

// TODO: if a "loaded" cache constant is not defined... until include_once()

if (!defined('GITAPIGET_CACHE_LIB')) {
    if (!defined('GITAPIGET_CACHE_LIB_PATH')) {
        define('GITAPIGET_CACHE_LIB_PATH', dirname(__FILE__).'/cache.php');
    }
    include_once(GITAPIGET_CACHE_LIB_PATH);
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

function get_gitapi_self_filelist_url($hash) {
    // debug('_SERVER', $_SERVER);
    // TODO: use a constant for the api_filelist file name
    // $result = 'http'.(strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? '' : 's').'//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
    $result = 'http'.(strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? '' : 's').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/api_filelist.php?hash='.$hash;
    // debug('result', $result);
    return $result;
    /*
    return strtr(
        'https://api.github.com/repos/$user/$repository/git/trees/$branch?recursive=1',
        array(
            '$user' => $user,
            '$repository' => $repository,
            '$branch' => isset($branch) ? $branch : 'master',
        )
    );
    */
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

/**
 * make an http call to the given url and return the result as text
 */
function get_gitapi_raw($url) {
    $result = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'aoloe/htdos-gitapi-get');
    // curl_setopt($ch, CURLOPT_HEADER, true);
    // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $result = curl_exec($ch);
    // debug('curl getinfo', curl_getinfo($ch));
    curl_close($ch);
    // debug('result', $result);
    return $result;
} // get_gitapi_raw()

/**
 * @param string $url
 * @param string $cache_id
 * @param array $cache
 */
function get_gitapi_list($url, $cache_id, $cache = null) {
    $result = array();
    // debug('url', $url);
    $raw = get_gitapi_raw($url);
    // debug('raw', $raw);
    $list = json_decode($raw, true);
    if (!isset($cache)) {
        $cache = get_gitapi_cache('gitapi/'.$cache_id.'/list.json');
    }
    if (is_string($cache) && $cache != '') {
        $cache = json_decode($cache);
    } elseif (!is_array($cache)) {
        $cache = array();
    }

    foreach ($list['tree'] as $item) {
        if ($item['type'] == 'blob') {
            if (array_key_exists($item['path'], $cache)) {
                $item['status'] = (($cache[$item['path']]['sha'] == $item['sha']) ? 'keep' : 'update');
                unset($cache[$item['path']]);
            } else {
                $item['status'] = 'new';
            }
        }
        $result[$item['path']] = $item;
    }
    foreach ($cache as $item) {
        $item['status'] = 'delete';
        $result[$item['path']] = $item;
    }
    // put_gitapi_cache('gitapi/'.$cache_id.'/list.json', encode_json($result));
    ksort($result);
    // debug('result', $result);
    return $result;
} // get_gitapi_list()

function get_gitapi_tree($url, $cache_id, $cache = null) {
    $result = array();
    $list = get_gitapi_list($url, $cache_id, $cache);
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

// $rate_limit = get_from_gitapi("https://api.github.com/rate_limit");
$rate_limit = json_decode(get_gitapi_raw("https://api.github.com/rate_limit"), true);
debug('rate_limit', $rate_limit);

// get the list of the files on github, check their status against the cached list and "format" it
// in a tree structure
if (false) {
    $tree = get_gitapi_tree(get_gitapi_github_filelist_url('aoloe', 'libregraphics-manual-scribus-development'), 'scribus-development');
} else {
    $tree = get_gitapi_tree(get_gitapi_self_filelist_url('lg_projects'), 'lg_projects');
}

debug('tree', $tree);

$action = array();

function update_gitapi_cache($tree, &$action) {
    foreach ($tree as $key => $value) {
        if (!array_key_exists('type', $value) || ($value['type'] == 'tree')) { // it's a file
        } else { // it's a directoy
        }
    }
} // update_gitapi_cache
