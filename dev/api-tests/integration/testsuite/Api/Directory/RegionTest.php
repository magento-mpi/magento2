<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Directory Region operations
 */
class Api_Directory_RegionTest extends Magento_Test_Webservice
{
    /**
     * Test region.list API method
     *
     * @return void
     */
    public function testList()
    {
        $data  = $this->call('region.list', array('country' => 'US'));
        $this->assertTrue(is_array($data), 'Region list is not array');
        $this->assertNotEmpty($data, 'Region list is empty');
        $region = reset($data);
        $this->assertTrue(is_string($region['name']) && strlen($region['name']),
            'Region name is empty or not a string');
    }
}
