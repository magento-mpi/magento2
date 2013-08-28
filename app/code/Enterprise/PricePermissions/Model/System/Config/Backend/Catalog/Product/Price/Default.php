<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PricePermission
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Default Product Price Backend Model
 *
 * @category    Enterprise
 * @package     Enterprise_PricePermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PricePermissions_Model_System_Config_Backend_Catalog_Product_Price_Default
    extends Magento_Core_Model_Config_Data
{
    /**
     * Price permissions data
     *
     * @var Enterprise_PricePermissions_Helper_Data
     */
    protected $_pricePermData = null;

    /**
     * @param Enterprise_PricePermissions_Helper_Data $pricePermData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Enterprise_PricePermissions_Helper_Data $pricePermData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_pricePermData = $pricePermData;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Check permission to edit product prices before the value is saved
     *
     * @return Enterprise_PricePermissions_Model_System_Config_Backend_Catalog_Product_Price_Default
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
     * @return Enterprise_PricePermissions_Model_System_Config_Backend_Catalog_Product_Price_Default
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
