<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'adminhtml_giftcardaccount';
        $this->_blockGroup = 'Enterprise_GiftCardAccount';
        $this->_headerText = Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Gift Card Accounts');
        $this->_addButtonLabel = Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Add Gift Card Account');
        parent::_construct();
    }
}
