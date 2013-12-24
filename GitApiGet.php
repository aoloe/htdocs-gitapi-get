<?php
/**
 * Object oriented proxy to the gitapi library to be used in Slim
 */
namespace GitApiGet;

require_once dirname(__FILE__).'/gitapi.php';
require_once dirname(__FILE__).'/cache.php';

class GitApiGet {
    protected $config = null;

    public function __construct($config = null) {
        $this->config = $config;
        // debug('config', $this->config);
    }

    public function get_ratelimit() {
        $url = isset($this->config) && array_key_exists('url.ratelimit', $this->config) ? $this->config['url.ratelimit'] : get_gitapi_github_ratelimit_url();
        // debug('url', $url);
        $curl = $this->get_curl_as_array($url);
        $limit = $curl['resources']['core']['limit'];
        return $limit;
    }

    public function get_list_from_cache() {
        $cache = get_gitapi_cache('list.json', $this->config['cache']);
        $list = ($cache != '' ? json_decode($cache, true) : array());
        debug('list', $list);
        return $list;
    }

    public function get_list() {
        $url = strtr(
            $this->config['url.filelist'],
            array(
                '$user' => $user,
                '$repository' => $repository,
                '$branch' => isset($branch) ? $branch : 'master',
            )
        );
        return get_gitapi_list($url, $cache);
    }

    public function get_compared_list($list, $cache) {
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
    public function update_cache($list, $path_cache, $url_raw, $use_get) {
        return update_gitapi_cache($list, $path_cache, $url_raw, $use_get);
    }
}
