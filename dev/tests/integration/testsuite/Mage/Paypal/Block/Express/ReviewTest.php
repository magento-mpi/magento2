<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Paypal_Block_Express_Review
 */
class Mage_Paypal_Block_Express_ReviewTest extends PHPUnit_Framework_TestCase
{
    public function testRenderAddress()
    {
        $block = new Mage_Paypal_Block_Express_Review;
        $addressData = include(__DIR__ . '/../../../Sales/_files/address_data.php');
        $address = new Mage_Sales_Model_Quote_Address($addressData);
        $address->setAddressType('billing');
        $this->assertContains('Los Angeles', $block->renderAddress($address));
    }
}
