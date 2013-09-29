<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config locale allowed currencies backend
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend;

class Locale extends \Magento\Core\Model\Config\Value
{

    /**
     * Enter description here...
     *
     * @return \Magento\Backend\Model\Config\Backend\Locale
     */
    protected function _afterSave()
    {
        $collection = \Mage::getModel('Magento\Core\Model\Config\Value')
            ->getCollection()
            ->addPathFilter('currency/options');

        $values     = explode(',', $this->getValue());
        $exceptions = array();

        foreach ($collection as $data) {
            $match = false;
            $scopeName = __('Default scope');

            if (preg_match('/(base|default)$/', $data->getPath(), $match)) {
                if (!in_array($data->getValue(), $values)) {
                    $currencyName = \Mage::app()->getLocale()->currency($data->getValue())->getName();
                    if ($match[1] == 'base') {
                        $fieldName = __('Base currency');
                    } else {
                        $fieldName = __('Display default currency');
                    }

                    switch ($data->getScope()) {
                        case 'default':
                            $scopeName = __('Default scope');
                            break;

                        case 'website':
                            $websiteName = \Mage::getModel('Magento\Core\Model\Website')
                                ->load($data->getScopeId())->getName();
                            $scopeName = __('website(%1) scope', $websiteName);
                            break;

                        case 'store':
                            $storeName = \Mage::getModel('Magento\Core\Model\Store')->load($data->getScopeId())
                                ->getName();
                            $scopeName = __('store(%1) scope', $storeName);
                            break;
                    }

                    $exceptions[] = __('Currency "%1" is used as %2 in %3.', $currencyName, $fieldName, $scopeName);
                }
            }
        }
        if ($exceptions) {
            \Mage::throwException(join("\n", $exceptions));
        }

        return $this;
    }

}
