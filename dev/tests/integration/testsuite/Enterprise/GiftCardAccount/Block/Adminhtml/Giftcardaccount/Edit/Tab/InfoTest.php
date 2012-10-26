<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftCardAccount
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info
 */
class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_InfoTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testInitForm()
    {
        Mage::register('current_giftcardaccount', Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount'));
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $block = $layout->addBlock('Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info');

        $element = $block->initForm()->getForm()->getElement('date_expires');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
