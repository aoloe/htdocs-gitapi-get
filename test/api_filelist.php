<?php
require_once(dirname(dirname(__FILE__)) . '/api_filelist.php');

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

class TestOfAPIFiliset extends UnitTestCase {
    function testGetFilelistEmpty() {
        $expected = array();
        // debug('file', __FILE__);
        // debug('dir', dirname(__FILE__));
        $result = get_gitapiget_filelist_tree(dirname(__FILE__).'/api_filelist/empty');
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistOneDir() {
        $expected = array(
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/onedir/test',
                 'url' => '???',
            ),
        );
        $result = get_gitapiget_filelist_tree(dirname(__FILE__).'/api_filelist/onedir');
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }
    function testGetFilelistTwoDirs() {
        $dir = 'twodirs';
        $expected = array(
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/'.$dir.'/retest',
                 'url' => '???',
            ),
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/'.$dir.'/test',
                 'url' => '???',
            ),
        );
        $result = get_gitapiget_filelist_tree(dirname(__FILE__).'/api_filelist/'.$dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistTwoDirsOneEmptyFile() {
        $dir = 'twodirsoneemptyfile';
        $expected = array(
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/'.$dir.'/retest',
                 'url' => '???',
            ),
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/'.$dir.'/test',
                 'url' => '???',
            ),
            array (
                 'type' => 'blob',
                 'sha' => 'e69de29bb2d1d6434b8b29ae775ad8c2e48c5391',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/'.$dir.'/test/test.txt',
                 'url' => '???',
            ),
        );
        $result = get_gitapiget_filelist_tree(dirname(__FILE__).'/api_filelist/'.$dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistTwoDirsOneFile() {
        $dir = 'twodirsonefile';
        $expected = array(
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/'.$dir.'/retest',
                 'url' => '???',
            ),
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/'.$dir.'/test',
                 'url' => '???',
            ),
            array (
                 'type' => 'blob',
                 'sha' => '4871fd52755be519c29ae719f385bd5f2863627c',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/'.$dir.'/test/test.txt',
                 'url' => '???',
            ),
        );
        $result = get_gitapiget_filelist_tree(dirname(__FILE__).'/api_filelist/'.$dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistTwoFilesGitignoreSwp() {
        $dir = 'twofilesgitignoreswp';
        $expected = array(
            array (
                 'type' => 'blob',
                 'sha' => '4871fd52755be519c29ae719f385bd5f2863627c',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/'.$dir.'/test.txt',
                 'url' => '???',
            ),
            array (
                 'type' => 'blob',
                 'sha' => 'a01ee289f9a3c65287845c5138783d4f3b24a443',
                 'path' => '/home/ale/docs/src/htdocs-gitapi-get/test/api_filelist/'.$dir.'/.gitignore.txt',
                 'url' => '???',
            ),
        );
        $result = get_gitapiget_filelist_tree(dirname(__FILE__).'/api_filelist/'.$dir);
        debug('result', $result);
        debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }
}
