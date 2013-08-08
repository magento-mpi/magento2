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
 * Config locale allowed currencies backend
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Locale extends Magento_Core_Model_Config_Data
{

    /**
     * Enter description here...
     *
     * @return Mage_Backend_Model_Config_Backend_Locale
     */
    protected function _afterSave()
    {
        $collection = Mage::getModel('Magento_Core_Model_Config_Data')
            ->getCollection()
            ->addPathFilter('currency/options');

        $values     = explode(',', $this->getValue());
        $exceptions = array();

        foreach ($collection as $data) {
            $match = false;
            $scopeName = Mage::helper('Mage_Backend_Helper_Data')->__('Default scope');

            if (preg_match('/(base|default)$/', $data->getPath(), $match)) {
                if (!in_array($data->getValue(), $values)) {
                    $currencyName = Mage::app()->getLocale()->currency($data->getValue())->getName();
                    if ($match[1] == 'base') {
                        $fieldName = Mage::helper('Mage_Backend_Helper_Data')->__('Base currency');
                    } else {
                        $fieldName = Mage::helper('Mage_Backend_Helper_Data')->__('Display default currency');
                    }

                    switch ($data->getScope()) {
                        case 'default':
                            $scopeName = Mage::helper('Mage_Backend_Helper_Data')->__('Default scope');
                            break;

                        case 'website':
                            $websiteName = Mage::getModel('Magento_Core_Model_Website')
                                ->load($data->getScopeId())->getName();
                            $scopeName = Mage::helper('Mage_Backend_Helper_Data')
                                ->__('website(%s) scope', $websiteName);
                            break;

                        case 'store':
                            $storeName = Mage::getModel('Magento_Core_Model_Store')->load($data->getScopeId())
                                ->getName();
                            $scopeName = Mage::helper('Mage_Backend_Helper_Data')->__('store(%s) scope', $storeName);
                            break;
                    }

                    $exceptions[] = Mage::helper('Mage_Backend_Helper_Data')
                        ->__('Currency "%s" is used as %s in %s.', $currencyName, $fieldName, $scopeName);
                }
            }
        }
        if ($exceptions) {
            Mage::throwException(join("\n", $exceptions));
        }

        return $this;
    }

}
