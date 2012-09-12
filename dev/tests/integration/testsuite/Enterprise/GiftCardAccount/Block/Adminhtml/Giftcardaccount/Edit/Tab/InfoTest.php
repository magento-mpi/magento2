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
 * Test class for Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info.
 *
 * @group module:Enterprise_GiftCardAccount
 */
class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_InfoTest
    extends PHPUnit_Framework_TestCase
{
    public function testInitForm()
    {
        $model = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount');
        Mage::register('current_giftcardaccount', $model);
        $layout = new Mage_Core_Model_Layout;
        $block = $layout->addBlock('Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info');

        $element = $block->initForm()->getForm()->getElement('date_expires');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
