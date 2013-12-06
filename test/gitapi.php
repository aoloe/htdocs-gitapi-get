<?php
require_once(dirname(dirname(__FILE__)) . '/cache.php');

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

class TestOfGitapi extends UnitTestCase {
    function testGetRaw() {
        // debug('_SERVER', $_SERVER);
        // debug('_SERVER[DOCUMENT_ROOT]', $_SERVER['DOCUMENT_ROOT']);
        // debug('_SERVER[SCRIPT_FILENAME]', $_SERVER['SCRIPT_FILENAME']);
        $path = substr(dirname($_SERVER['SCRIPT_FILENAME']), strlen($_SERVER['DOCUMENT_ROOT']));
        // debug('path', $path);
        // TODO: implement this test!
        $result = 'http'.(strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? '' : 's').'://'.$_SERVER['HTTP_HOST'].'/'.$path.'/api_fileraw.php'.(isset($hash) ? '?hash='.$hash : '');
        /*
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
        */
    }
}
