<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'adminhtml_giftcardaccount';
        $this->_blockGroup = 'Magento_GiftCardAccount';
        $this->_headerText = Mage::helper('Magento_GiftCardAccount_Helper_Data')->__('Gift Card Accounts');
        $this->_addButtonLabel = Mage::helper('Magento_GiftCardAccount_Helper_Data')->__('Add Gift Card Account');
        parent::_construct();
    }
}
