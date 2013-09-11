<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit;

class Form extends \Magento\Adminhtml\Block\Widget\Form
{

    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $giftcardaccount = \Mage::registry('current_giftcardaccount');

        if ($giftcardaccount->getId()) {
            $form->addField('giftcardaccount_id', 'hidden', array(
                'name' => 'giftcardaccount_id',
            ));
            $form->setValues($giftcardaccount->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
