<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Sales
 */
class Mage_Sales_Block_Recurring_Profile_ViewTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareAddressInfo()
    {
        $profile = new Mage_Sales_Model_Recurring_Profile;
        $profile->setData('billing_address_info', Mage_Sales_Utility_Address::getAddress('billing'));
        Mage::register('current_recurring_profile', $profile);

        $block = new Mage_Sales_Block_Recurring_Profile_View;
        $block->setLayout(new Mage_Core_Model_Layout);
        $block->prepareAddressInfo();
        $info = $block->getRenderedInfo();
        $this->assertContains('Los Angeles', $info[0]->getValue());
    }
}
