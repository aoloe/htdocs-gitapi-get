<?php
require_once(dirname(dirname(__FILE__)) . '/api_filelist.php');

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

class TestOfAPIFiliset extends UnitTestCase {
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
    function testGetFilelistEmpty() {
        $expected = array();
        $dir = dirname(__FILE__).'/api_filelist/empty';
        $this->ensureDirectory($dir);
        // debug('file', __FILE__);
        // debug('dir', dirname(__FILE__));
        $result = get_gitapiget_filelist_tree($dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistOneDir() {
        $dir = dirname(__FILE__).'/api_filelist/onedir';
        $this->ensureDirectory($dir);
        $this->ensureDirectory($dir.'/test');
        $expected = array(
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => 'test',
                 'url' => '???',
            ),
        );
        $result = get_gitapiget_filelist_tree($dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }
    function testGetFilelistTwoDirs() {
        $dir = dirname(__FILE__).'/api_filelist/twodirs';
        $this->ensureDirectory($dir);
        $this->ensureDirectory($dir.'/retest');
        $this->ensureDirectory($dir.'/test');
        $expected = array(
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => 'retest',
                 'url' => '???',
            ),
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => 'test',
                 'url' => '???',
            ),
        );
        $result = get_gitapiget_filelist_tree($dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistTwoDirsOneEmptyFile() {
        $dir = dirname(__FILE__).'/api_filelist/twodirsoneemptyfile';
        $this->ensureDirectory($dir.'/retest');
        $expected = array(
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => 'retest',
                 'url' => '???',
            ),
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => 'test',
                 'url' => '???',
            ),
            array (
                 'type' => 'blob',
                 'sha' => 'e69de29bb2d1d6434b8b29ae775ad8c2e48c5391',
                 'path' => 'test/test.txt',
                 'url' => '???',
            ),
        );
        $result = get_gitapiget_filelist_tree($dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistTwoDirsOneFile() {
        $dir = dirname(__FILE__).'/api_filelist/twodirsonefile';
        $this->ensureDirectory($dir.'/retest');
        $expected = array(
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => 'retest',
                 'url' => '???',
            ),
            array (
                 'type' => 'tree',
                 'sha' => '',
                 'path' => 'test',
                 'url' => '???',
            ),
            array (
                 'type' => 'blob',
                 'sha' => '4871fd52755be519c29ae719f385bd5f2863627c',
                 'path' => 'test/test.txt',
                 'url' => '???',
            ),
        );
        $result = get_gitapiget_filelist_tree($dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistPathMatchesSwp() {

        $expected = true;
        $result = path_matches('test/.text.swp', '.*.swp');
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistPathMatchesSwpInArray() {

        $expected = true;
        $result = path_matches('test/.text.swp', array('.*.swp', 'trust'));
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistPathNotMatchesSwp() {

        $expected = false;
        $result = path_matches('test/.text', '.*.swp');
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistPathNotMatchesSwpInArray() {

        $expected = false;
        $result = path_matches('test/.text', array('.*.swp', 'trust'));
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }

    function testGetFilelistTwoFilesGitignoreSwp() {
        $dir = dirname(__FILE__).'/api_filelist/twofilesgitignoreswp';
        $expected = array(
            array (
                 'type' => 'blob',
                 'sha' => '4871fd52755be519c29ae719f385bd5f2863627c',
                 'path' => 'test.txt',
                 'url' => '???',
            ),
            array (
                 'type' => 'blob',
                 'sha' => 'a01ee289f9a3c65287845c5138783d4f3b24a443',
                 'path' => '.gitignore',
                 'url' => '???',
            ),
        );
        $result = get_gitapiget_filelist_tree($dir);
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }
}

