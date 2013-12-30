<?php
/**
 * Object oriented proxy to the gitapi library to be used in Slim
 */
namespace Aoloe\GitApiGet;

require_once dirname(__FILE__).'/gitapi.php';
require_once dirname(__FILE__).'/cache.php';

class GitApiGet {
    protected $config = null;

    public function __construct($config = null) {
        $this->config = $config;
        // debug('config', $this->config);
        // ensure_gitapi_directory_writable($this->config['cache.path']);
    }

    public function get_ratelimit() {
        $url = isset($this->config) && array_key_exists('url.ratelimit', $this->config) ? $this->config['url.ratelimit'] : get_gitapi_github_ratelimit_url();
        // debug('url', $url);
        $curl = $this->get_curl_as_array($url);
        $limit = $curl['resources']['core']['limit'];
        return $limit;
    }

    public function get_list_from_cache() {
        $cache = get_gitapi_cache('list.json', $this->config['cache.path']);
        $list = ($cache != '' ? json_decode($cache, true) : array());
        // debug('list', $list);
        return $list;
    }

    public function set_list_into_cache($list) {
        put_gitapi_cache('list.json', json_encode($list), $this->config['cache.path']);
        return $list;
    }

    public function get_list() {
        $url = strtr(
            $this->config['url.filelist'],
            // TODO: find a way to show different repositories (forks) and branches
            array(
                '$user' => $this->config['repository.user'],
                '$repository' => $this->config['repository.repository'],
                '$branch' => $this->config['repository.branch'],
            )
        );
        // debug('url', $url);
        return get_gitapi_list($url);
    }

    public function get_list_compared($list, $cache) {
        return get_gitapi_compared_list($list, $cache);
    }

    // TODO: find a better name
    public function get_curl_result($url) {
        return get_gitapi_raw($url);
    }

    // TODO: find a better name
    public function get_curl_as_array($url) {
        return json_decode($this->get_curl_result($url), true);
    }

    public function ensure_cache($path_cache) {
        ensure_gitapi_cache($path_cache);
    }
    public function update_cache($list) {
        return update_gitapi_cache($list, $this->config['cache.path'], $this->config['url.fileraw'], array_key_exists('url.method', $this->config) && $this->config['url.method'] == 'get');
    }
}
