<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Google Optimizer Source Model
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Model_Adminhtml_System_Config_Source_Googleoptimizer_Conversionpages
{
    
    public function toOptionArray()
    {
        return array(
            array('value' => '',                                'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('-- Please Select --')),
            array('value' => 'other',                           'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Other')),
            array('value' => 'checkout_cart',                   'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Shopping Cart')),
            array('value' => 'checkout_onepage',                'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('One Page Checkout')),
            array('value' => 'checkout_multishipping',          'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Multi Address Checkout')),
            array('value' => 'checkout_onepage_success',        'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Order Success (One Page Checkout)')),
            array('value' => 'checkout_multishipping_success',  'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Order Success (Multi Address Checkout)')),
            array('value' => 'customer_account_create',         'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Account Registration')),
        );
    }
    
}
