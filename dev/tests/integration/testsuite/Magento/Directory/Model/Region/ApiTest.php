<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
/**
 * Test Directory Region operations
 */
class Magento_Directory_Model_Region_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test directoryRegionList API method
     */
    public function testList()
    {
        $data = Magento_TestFramework_Helper_Api::call($this, 'directoryRegionList', array('country' => 'US'));
        $this->assertTrue(is_array($data), 'Region list is not array');
        $this->assertNotEmpty($data, 'Region list is empty');
        $region = reset($data);
        $this->assertTrue(
            is_string($region['name']) && strlen($region['name']),
            'Region name is empty or not a string'
        );
    }
}
