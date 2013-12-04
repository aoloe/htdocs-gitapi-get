<?php
require_once(dirname(dirname(__FILE__)) . '/treepath.php');

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

class TestOfSetTreePath extends UnitTestCase {
    function testSetRootEmptyItem() {
        $array = array();
        $output = array('count' => 0, 'tree' => array());
        set_gitapi_array_path($array, '', array());
        // debug('array', $array);
        // debug('output', $output);
        $this->assertIdentical($output, $array);
    }

    function testSetRootItem() {
        $array = array();
        $output = array('count' => 1, 'tree' => array(), 'abcd');
        set_gitapi_array_path($array, '', array('abcd'));
        // debug('array', $array);
        // debug('output', $output);
        $this->assertIdentical($output, $array);
    }

    function testSetFirstLevelDirectory() {
        $array = array();
        $output = array('count' => 0, 'tree' => array('test' => array()));
        set_gitapi_array_path($array, 'test', array());
        // debug('array', $array);
        // debug('output', $output);
        $this->assertIdentical($output, $array);
    }

    function testSetEmptyFirstLevelItem() {
        $array = array();
        $output = array(
            'count' => 0,
            'tree' => array(
                'test' => array(
                    'count' => 0,
                    'tree' => array(
                        'text.txt' => array(),
                    )
                )
            )
        );
        set_gitapi_array_path($array, 'test/text.txt', array());
        // debug('array', $array);
        // debug('output', $output);
        $this->assertIdentical($output, $array);
    }

    function testSetFirstLevelItem() {
        $array = array();
        $output = array(
            'count' => 1,
            'tree' => array(
                'test' => array(
                    'count' => 1,
                    'tree' => array(
                        'text.txt' => array('name' => 'test', 'hash' => '1234'),
                    )
                )
            )
        );
        set_gitapi_array_path($array, 'test/text.txt', array('name' => 'test', 'hash' => '1234'));
        // debug('array', $array);
        // debug('output', $output);
        $this->assertIdentical($output, $array);
    }

    function testSetSecondLevelItem() {
        $array = array();
        $output = array(
            'count' => 1,
            'tree' => array(
                'test' => array(
                    'count' => 1,
                    'tree' => array(
                        'toast' => array(
                            'count' => 1,
                            'tree' => array(
                                'text.txt' => array('name' => 'test', 'hash' => '1234'),
                            )
                        )
                    )
                )
            )
        );
        set_gitapi_array_path($array, 'test/toast/text.txt', array('name' => 'test', 'hash' => '1234'));
        // debug('array', $array);
        // debug('output', $output);
        $this->assertIdentical($output, $array);
    }

    function testSetSecondFirstLevelItem() {
        $array = array();
        $output = array(
            'count' => 2,
            'tree' => array(
                'test' => array(
                    'count' => 2,
                    'tree' => array(
                        'text.txt' => array('name' => 'test', 'hash' => '1234'),
                        'toxt.txt' => array('name' => 'tost', 'hash' => '4321'),
                    )
                )
            )
        );
        set_gitapi_array_path($array, 'test/text.txt', array('name' => 'test', 'hash' => '1234'));
        set_gitapi_array_path($array, 'test/toxt.txt', array('name' => 'tost', 'hash' => '4321'));
        // debug('array', $array);
        // debug('output', $output);
        $this->assertIdentical($output, $array);
    }
}
