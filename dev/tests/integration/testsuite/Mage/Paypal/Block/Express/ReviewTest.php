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
 * @group module:Mage_Core
 */
/**
 * Test class for Mage_Core_Model_Layout_Structure.
 */
class Mage_Paypal_Block_Express_ReviewTest extends PHPUnit_Framework_TestCase
{
    public function testRenderAddress()
    {
        $block = new Mage_Paypal_Block_Express_Review;
        $address = new Mage_Sales_Model_Quote_Address;
        $address->setRegion('CA')
            ->setPostcode('11111')
            ->setFirstname('firstname')
            ->setLastname('lastname')
            ->setStreet('street')
            ->setCity('Los Angeles')
            ->setEmail('admin@example.com')
            ->setTelephone('1111111111')
            ->setCountryId('US')
            ->setAddressType('billing');
        $this->assertContains('Los Angeles', $block->renderAddress($address));
    }
}
