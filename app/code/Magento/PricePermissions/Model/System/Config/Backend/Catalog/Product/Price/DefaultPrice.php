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
     * Price permissions data
     *
     * @var \Magento\PricePermissions\Helper\Data
     */
    protected $_pricePermData = null;

    /**
     * @param \Magento\PricePermissions\Helper\Data $pricePermData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\PricePermissions\Helper\Data $pricePermData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_pricePermData = $pricePermData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Check permission to edit product prices before the value is saved
     *
     * @return \Magento\PricePermissions\Model\System\Config\Backend\Catalog\Product\Price\DefaultPrice
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $defaultProductPriceValue = floatval($this->getValue());
        if (!$this->_pricePermData->getCanAdminEditProductPrice()
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
        if (!$this->_pricePermData->getCanAdminReadProductPrice()) {
            $this->setValue(null);
        }
        return $this;
    }
}
