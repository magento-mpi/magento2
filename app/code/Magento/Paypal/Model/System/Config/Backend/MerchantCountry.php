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
 * Backend model for merchant country. Default country used instead of empty value.
 */
namespace Magento\Paypal\Model\System\Config\Backend;

class MerchantCountry extends \Magento\Core\Model\Config\Value
{
    /**
     * Substitute empty value with Default country.
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (empty($value)) {
            if ($this->getWebsite()) {
                $defaultCountry = \Mage::app()->getWebsite($this->getWebsite())
                    ->getConfig(\Magento\Core\Helper\Data::XML_PATH_DEFAULT_COUNTRY);
            } else {
                $defaultCountry = \Mage::helper('Magento\Core\Helper\Data')->getDefaultCountry($this->getStore());
            }
            $this->setValue($defaultCountry);
        }
    }
}
