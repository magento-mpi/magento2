<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class FunctionsTest extends Unit_PHPUnit_TestCase
{
    /**
     * @covers array_replace_recursive
     * @test
     */
    public function arrayReplaceRecursiveExists()
    {
        $this->assertTrue(function_exists('array_replace_recursive'));
    }

    /**
     * @covers       array_replace_recursive
     * @dataProvider arrayReplaceRecursiveDataProvider
     * @test
     */
    public function arrayReplaceRecursive($arraySource, $arrayToMerge, $expected)
    {
        $result = array_replace_recursive($arraySource, $arrayToMerge);
        $this->assertNotNull($result);
        $this->assertEquals($result, $expected);
    }

    public function arrayReplaceRecursiveDataProvider()
    {
        return array(
            array(
                array(
                    'browser' => array('default' => array('browser' => 'chrome')),
                    'applications' => array('magento-ce')
                ),
                array('browser' => array('default' => array('browser' => 'firefox'), 'firefox')),
                array(
                    'browser' => array('default' => array('browser' => 'firefox'), 'firefox'),
                    'applications' => array('magento-ce')
                )
            )
        );
    }
}