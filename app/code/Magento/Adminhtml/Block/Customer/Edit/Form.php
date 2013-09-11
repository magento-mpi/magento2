<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer edit form block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Customer\Edit;

class Form extends \Magento\Adminhtml\Block\Widget\Form
{

    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));

        $customer = \Mage::registry('current_customer');

        if ($customer->getId()) {
            $form->addField('entity_id', 'hidden', array(
                'name' => 'customer_id',
            ));
            $form->setValues($customer->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
