<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $prefix = '_customerbalance';
        $form->setHtmlIdPrefix($prefix);
        $form->setFieldNameSuffix('customerbalance');

        $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));

        /** @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->addFieldset('storecreidt_fieldset',
            array('legend' => Mage::helper('enterprise_customerbalance')->__('Update Balance'))
        );

        if (!Mage::getSingleton('enterprise_customerbalance/balance')->shouldCustomerHaveOneBalance($customer)) {
            $fieldset->addField('website_id', 'select', array(
                'name'     => 'website_id',
                'label'    => Mage::helper('enterprise_customerbalance')->__('Website'),
                'title'    => Mage::helper('enterprise_customerbalance')->__('Website'),
                'values'   => Mage::getModel('adminhtml/system_store')->getWebsiteValuesForForm(),
                'onchange' => 'updateEmailWebsites()',
            ));
        }

        $fieldset->addField('amount_delta', 'text', array(
            'name'     => 'amount_delta',
            'label'    => Mage::helper('enterprise_customerbalance')->__('Update Balance'),
            'title'    => Mage::helper('enterprise_customerbalance')->__('Update Balance'),
            'comment'  => Mage::helper('enterprise_customerbalance')->__('An amount on which to change the balance'),
        ));

        $fieldset->addField('notify_by_email', 'checkbox', array(
            'name'     => 'notify_by_email',
            'label'    => Mage::helper('enterprise_customerbalance')->__('Notify customer by email'),
            'title'    => Mage::helper('enterprise_customerbalance')->__('Notify customer by email'),
            'after_element_html' => '<script type="text/javascript">'
                . "
                updateEmailWebsites();
                $('{$prefix}notify_by_email').disableSendemail = function() {
                    $('{$prefix}store_id').disabled = (this.checked) ? false : true;
                }.bind($('{$prefix}notify_by_email'));
                Event.observe('{$prefix}notify_by_email', 'click', $('{$prefix}notify_by_email').disableSendemail);
                $('{$prefix}notify_by_email').disableSendemail();
                "
                . '</script>'
        ));

        $fieldset->addField('store_id', 'select', array(
            'name'     => 'store_id',
            'label'    => Mage::helper('enterprise_customerbalance')->__('Send email notification from the following Store View'),
            'title'    => Mage::helper('enterprise_customerbalance')->__('Send email notification from the following Store View'),
        ));

        $form->setValues($customer->getData());
        $this->setForm($form);
        return $this;
    }
}
