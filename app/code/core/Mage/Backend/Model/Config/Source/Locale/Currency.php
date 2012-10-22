<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
/**
 * Locale currency source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Locale_Currency
{
    protected $_option;
    
    public function toOptionArray()
    {
        return Mage::app()->getLocale()->getOptionCurrencies();
    }
}
