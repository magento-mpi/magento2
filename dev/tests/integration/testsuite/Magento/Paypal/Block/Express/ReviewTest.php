<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Paypal_Block_Express_Review
 */
class Magento_Paypal_Block_Express_ReviewTest extends PHPUnit_Framework_TestCase
{
    public function testRenderAddress()
    {
        $block = Mage::app()->getLayout()->createBlock('Magento_Paypal_Block_Express_Review');
        $addressData = include(__DIR__ . '/../../../Sales/_files/address_data.php');
        $address = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Quote_Address', array('data' => $addressData));
        $address->setAddressType('billing');
        $this->assertContains('Los Angeles', $block->renderAddress($address));
    }
}
