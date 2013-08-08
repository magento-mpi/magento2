<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Tax_Model_Config_Price_Include extends Magento_Core_Model_Config_Data
{
    public function afterSave()
    {
        parent::afterSave();
        Mage::app()->cleanCache('checkout_quote');
    }
}
