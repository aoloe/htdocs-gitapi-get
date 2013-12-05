<?php
require_once(dirname(dirname(__FILE__)) . '/cache.php');

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

class TestOfCache extends UnitTestCase {
    /**
     * @return the list of all files in a directory, except . and ..
     */
    private function ls($path) {
        $result = array();
        if ($handle = opendir($path)) {
            while (false !== ($filename = readdir($handle))) {
                if (($filename != '.') && ($filename != '..')) {
                    $result[] = $filename;
                }
            }
        }
        sort($result);
        return $result;
    }

    function testPutCacheFile() {
        $dir = 'file';
        $expected = array();
        $result = $this->ls(dirname(__FILE__).'/');
        // debug('file', __FILE__);
        // debug('dir', dirname(__FILE__));
        // $result = get_gitapiget_filelist_tree(dirname(__FILE__).'/api_filelist/empty');
        debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }
}
