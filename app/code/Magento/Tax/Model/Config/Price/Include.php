<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Tax_Model_Config_Price_Include extends Magento_Core_Model_Config_Value
{
    public function afterSave()
    {
        parent::afterSave();
        Mage::app()->cleanCache('checkout_quote');
    }
}
