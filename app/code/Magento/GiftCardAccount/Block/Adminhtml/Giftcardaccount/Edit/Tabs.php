<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftcardaccount_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Magento_GiftCardAccount_Helper_Data')->__('Gift Card Account'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('info', array(
            'label'     => Mage::helper('Magento_GiftCardAccount_Helper_Data')->__('Information'),
            'content'   => $this->getLayout()->createBlock(
                'Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info'
            )->initForm()->toHtml(),
            'active'    => true
        ));

        $this->addTab('send', array(
            'label'     => Mage::helper('Magento_GiftCardAccount_Helper_Data')->__('Send Gift Card'),
            'content'   => $this->getLayout()->createBlock(
                'Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Send'
            )->initForm()->toHtml(),
        ));

        $model = Mage::registry('current_giftcardaccount');
        if ($model->getId()) {
            $this->addTab('history', array(
                'label'     => Mage::helper('Magento_GiftCardAccount_Helper_Data')->__('History'),
                'content'   => $this->getLayout()->createBlock(
                    'Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_History'
                )->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }

}
