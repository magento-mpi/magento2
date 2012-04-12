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
        $address = Mage_Sales_Utility_Address::getQuoteAddress('billing');
        $this->assertContains('Los Angeles', $block->renderAddress($address));
    }
}
