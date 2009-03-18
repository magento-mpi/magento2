<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $prefix = '_customerbalance';
        $form->setHtmlIdPrefix($prefix);
        $form->setFieldNameSuffix('customerbalance');

        $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));

        #FIXME:
        $ballance = new Varien_Object();

        /** @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->addFieldset('storecreidt_fieldset',
            array('legend' => Mage::helper('enterprise_customerbalance')->__('Update Balance'))
        );

        if( !Mage::getStoreConfig('customer/account_share/scope') ) {
            $fieldset->addField('website', 'select', array(
                    'name'      => 'website',
                    'label'     => Mage::helper('enterprise_customerbalance')->__('Website'),
                    'title'     => Mage::helper('enterprise_customerbalance')->__('Website'),
                    'values'    => Mage::getModel('adminhtml/system_store')->getWebsiteValuesForForm(),
                    'onchange'  => 'updateEmailWebsites()',
                )
            );
        }

        $fieldset->addField('delta', 'text', array(
                'name'      => 'delta',
                'label'     => Mage::helper('enterprise_customerbalance')->__('Update Balance'),
                'title'     => Mage::helper('enterprise_customerbalance')->__('Update Balance'),
                'comment'   => Mage::helper('enterprise_customerbalance')->__('An amount on which to change the balance'),
            )
        );

        $sendemail = $fieldset->addField('email_notify', 'checkbox', array(
                'name'      => 'email_notify',
                'label'     => Mage::helper('enterprise_customerbalance')->__('Notify customer by email'),
                'title'     => Mage::helper('enterprise_customerbalance')->__('Notify customer by email'),
            )
        );

        $fieldset->addField('trans_email_store', 'select', array(
                'name'      => 'trans_email_store',
                'label'     => Mage::helper('enterprise_customerbalance')->__('Send email notification from the following Store View'),
                'title'     => Mage::helper('enterprise_customerbalance')->__('Send email notification from the following Store View'),
                'disabled'  => $ballance->getEmailNotify() ? false : true,
            )
        );

        $sendemail->setAfterElementHtml(
                '<script type="text/javascript">'
                . "
                updateEmailWebsites();
                $('{$prefix}email_notify').disableSendemail = function() {
                    $('{$prefix}trans_email_store').disabled = (this.checked) ? false : true;
                }.bind($('{$prefix}email_notify'));
                Event.observe('{$prefix}email_notify', 'click', $('{$prefix}email_notify').disableSendemail);
                $('{$prefix}email_notify').disableSendemail();
                "
                . '</script>'
        );

        $form->setValues($customer->getData());
        $this->setForm($form);
        return $this;
    }
}