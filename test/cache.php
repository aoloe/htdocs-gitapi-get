<?php
require_once(dirname(dirname(__FILE__)) . '/cache.php');

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

class TestOfCache extends UnitTestCase {

    function __construct() {
        parent::__construct();
        $this->ensureDirectory(dirname(__FILE__).'/cache');
    }

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

    private function ensureDirectory($dir) {
        if (!file_exists($dir)) {
            if (is_writable(dirname($dir))) {
                    mkdir($dir);
            } else {
                $html = new HtmlReporter();
                $html->paintFail(dirname($dir).' is not writable');
                die();
            }
        }
    }

    function testPutCacheAddFile() {
        $dir = dirname(__FILE__).'/cache/file';
        $content = 'this is a test content';
        $this->ensureDirectory($dir);
        $expected = array('test.txt');
        put_gitapi_cache('test.txt', $content, $dir);
        $result = $this->ls($dir);
        // debug('result', $result);
        // debug('expected', $expected);
        unlink($dir.'/test.txt');
        $this->assertIdentical($result, $expected);
    }

    function testPutCacheAddFileUrl() {
        $dir = dirname(__FILE__).'/cache/fileurl';
        $this->ensureDirectory($dir);
        $content = 'this is a test content';
        $expected = array(urlencode('content/test.txt'));
        put_gitapi_cache('content/test.txt', $content, $dir);
        $result = $this->ls($dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);


        unlink($dir.'/'.urlencode('content/test.txt'));
    }

    function testPutCacheAddGetFile() {
        $dir = dirname(__FILE__).'/cache/addgetfile';
        $this->ensureDirectory($dir);
        $content = 'this is a test content';
        $expected = $content;
        put_gitapi_cache('content/test.txt', $content, $dir);
        $result = get_gitapi_cache('content/test.txt', $dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);

        unlink($dir.'/'.urlencode('content/test.txt'));
    }

    function testPutCacheAddDelFile() {
        $dir = dirname(__FILE__).'/cache/adddelfile';
        $this->ensureDirectory($dir);
        $content = 'this is a test content';
        $expected = array(urlencode('content/test.txt'));
        put_gitapi_cache('content/test.txt', $content, $dir);
        $result = $this->ls($dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);

        $expected = array();
        delete_gitapi_cache('content/test.txt', $dir);
        $result = $this->ls($dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }
}
