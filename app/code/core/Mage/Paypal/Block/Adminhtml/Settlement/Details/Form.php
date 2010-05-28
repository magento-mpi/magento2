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
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Settlement reports transaction details
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Adminhtml_Settlement_Details_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('current_transaction');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('paypal')->__('Transaction Details')));

        $fields = array(
            'transaction_id' => array('label' => Mage::helper('paypal')->__('Transaction ID')),
//            'invoice_id' => Mage::helper('paypal')->__(''),
//            'paypal_reference_id' => Mage::helper('paypal')->__(''),
//            'paypal_reference_id_type' => Mage::helper('paypal')->__(''),
//            'transaction_event_code' => Mage::helper('paypal')->__(''),
            'transaction_initiation_date' => array('label' => Mage::helper('paypal')->__('Start Date'), 'value' => $this->helper('core')->formatDate($model->getData('transaction_initiation_date'))),
//            'transaction_completion_date' => Mage::helper('paypal')->__(''),
//            'transaction_debit_or_credit' => Mage::helper('paypal')->__(''),
//            'gross_transaction_amount' => Mage::helper('paypal')->__(''),
//            'gross_transaction_currency' => Mage::helper('paypal')->__(''),
//            'fee_debit_or_credit' => Mage::helper('paypal')->__(''),
//            'fee_amount' => Mage::helper('paypal')->__(''),
//            'fee_currency' => Mage::helper('paypal')->__(''),
//            'custom_field' => Mage::helper('paypal')->__(''),
//            'consumer_id' => Mage::helper('paypal')->__(''),
        );

        foreach ($fields as $key => $field) {
            $fieldset->addField($key, 'label', array(
                'name'  => $key,
                'label' => $field['label'],
                'title' => $field['label'],
                'value' => isset($field['value']) ? $field['value'] : $model->getData($key),
//                'class'     => '',
//                'note'      => '',
            ));
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
