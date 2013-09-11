<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_giftcardaccount';
        $this->_blockGroup = 'Magento_GiftCardAccount';

        parent::_construct();

        $clickSave = "\$('_sendaction').value = 0;";
        $clickSave .= "\$('_sendrecipient_email').removeClassName('required-entry');";
        $clickSave .= "\$('_sendrecipient_name').removeClassName('required-entry');";

        $this->_updateButton('save', 'label', __('Save'));
        $this->_updateButton('save', 'onclick', $clickSave);
        $this->_updateButton('save', 'data_attribute', array(
            'mage-init' => array(
                'button' => array('event' => 'save', 'target' => '#edit_form'),
            ),
        ));
        $this->_updateButton('delete', 'label', __('Delete'));

        $clickSend = "\$('_sendrecipient_email').addClassName('required-entry');";
        $clickSend .= "\$('_sendrecipient_name').addClassName('required-entry');";
        $clickSend .= "\$('_sendaction').value = 1;";

        $this->_addButton('send', array(
            'label'     => __('Save & Send Email'),
            'onclick'   => $clickSend,
            'class'     => 'save',
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#edit_form'),
                ),
            )
        ));
    }

    public function getGiftcardaccountId()
    {
        return \Mage::registry('current_giftcardaccount')->getId();
    }

    public function getHeaderText()
    {
        if (\Mage::registry('current_giftcardaccount')->getId()) {
            return __('Edit Gift Card Account: %1', $this->escapeHtml(\Mage::registry('current_giftcardaccount')->getCode()));
        }
        else {
            return __('New Gift Card Account');
        }
    }

}
