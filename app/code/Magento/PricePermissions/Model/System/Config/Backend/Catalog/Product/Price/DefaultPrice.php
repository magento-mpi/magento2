<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Default Product Price Backend Model
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PricePermissions\Model\System\Config\Backend\Catalog\Product\Price;

class DefaultPrice
    extends \Magento\Core\Model\Config\Value
{
    /**
     * Check permission to edit product prices before the value is saved
     *
     * @return \Magento\PricePermissions\Model\System\Config\Backend\Catalog\Product\Price\DefaultPrice
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $defaultProductPriceValue = floatval($this->getValue());
        if (!\Mage::helper('Magento\PricePermissions\Helper\Data')->getCanAdminEditProductPrice()
            || ($defaultProductPriceValue < 0)
        ) {
            $defaultProductPriceValue = floatval($this->getOldValue());
        }
        $this->setValue((string)$defaultProductPriceValue);
        return $this;
    }

    /**
     * Check permission to read product prices before the value is shown to user
     *
     * @return \Magento\PricePermissions\Model\System\Config\Backend\Catalog\Product\Price\DefaultPrice
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if (!\Mage::helper('Magento\PricePermissions\Helper\Data')->getCanAdminReadProductPrice()) {
            $this->setValue(null);
        }
        return $this;
    }
}
