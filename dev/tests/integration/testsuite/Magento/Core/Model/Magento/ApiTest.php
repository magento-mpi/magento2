<?php
/**
 * Core module API tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/**
 * Magento info Api tests
 */
class Magento_Core_Model_Magento_ApiTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Api tests were skipped');
    }

    /**
     * Test magento magento info retrieving
     */
    public function testInfo()
    {
        $magentoInfo = Magento_Test_Helper_Api::call($this, 'magentoInfo');
        $this->assertNotEmpty($magentoInfo['magento_version']);
        $this->assertNotEmpty($magentoInfo['magento_edition']);
        $this->assertEquals(Mage::getEdition(), $magentoInfo['magento_edition']);
    }
}
