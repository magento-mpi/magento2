<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test API2 data helper
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Helper_DataTest extends Magento_TestCase
{
    /**
     * Test get interpreter type for Request body according to Content-type HTTP header
     *
     * @return void
     */
    public function testGetRequestInterpreterAdapters()
    {
        /** @var $helper Mage_Api2_Helper_Data */
        $helper = Mage::helper('api2');

        $adapters = $helper->getRequestInterpreterAdapters();

        $this->assertInternalType('array', $adapters);
        $this->assertGreaterThan(0, count($adapters));
    }

    /**
     * Test get interpreter type for Request body according to Content-type HTTP header
     *
     * @return void
     */
    public function testGetResponseRenderAdapters()
    {
        /** @var $helper Mage_Api2_Helper_Data */
        $helper = Mage::helper('api2');

        $adapters = $helper->getResponseRenderAdapters();

        $this->assertInternalType('array', $adapters);
        $this->assertGreaterThan(0, count($adapters));
    }
}
