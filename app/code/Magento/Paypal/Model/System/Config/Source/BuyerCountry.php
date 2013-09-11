<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for buyer countries supported by PayPal
 */
namespace Magento\Paypal\Model\System\Config\Source;

class BuyerCountry
{
    public function toOptionArray($isMultiselect = false)
    {
        $supported = \Mage::getModel('Magento\Paypal\Model\Config')->getSupportedBuyerCountryCodes();
        $options = \Mage::getResourceModel('Magento\Directory\Model\Resource\Country\Collection')
            ->addCountryCodeFilter($supported, 'iso2')
            ->loadData()
            ->toOptionArray($isMultiselect ? false : __('--Please Select--'));

        return $options;
    }
}
