<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();
$acceptedCurrencyConfig = Mage::getStoreConfig('payment/eway_direct/currency');
$baseCurrencyConfig = Mage::getStoreConfig('currency/options/base');
//check wrong setup
if ($baseCurrencyConfig != 'AUD' && $acceptedCurrencyConfig != $baseCurrencyConfig) {
    //disable eWAY Direct: default scope and all websites
    Mage::getConfig()->deleteConfig('payment/eway_direct/active');
    foreach (Mage::app()->getWebsites() as $website) {
        Mage::getConfig()->deleteConfig('payment/eway_direct/active', 'websites', (int)$website->getId());
    }
}
//delete currency restriction
Mage::getConfig()->deleteConfig('payment/eway_direct/currency');

/**
 * Change "sagepay_direct" method name to "pbridge_sagepay_direct" in table of orders
 */
$installer->getConnection()->update(
    $installer->getTable('Mage_Sales_Model_Order_Payment'),
    array('method' => 'pbridge_sagepay_direct'),
    array('method = ?' => 'sagepay_direct')
);
