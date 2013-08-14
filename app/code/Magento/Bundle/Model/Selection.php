<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle Selection Model
 *
 * @method Magento_Bundle_Model_Resource_Selection _getResource()
 * @method Magento_Bundle_Model_Resource_Selection getResource()
 * @method int getOptionId()
 * @method Magento_Bundle_Model_Selection setOptionId(int $value)
 * @method int getParentProductId()
 * @method Magento_Bundle_Model_Selection setParentProductId(int $value)
 * @method int getProductId()
 * @method Magento_Bundle_Model_Selection setProductId(int $value)
 * @method int getPosition()
 * @method Magento_Bundle_Model_Selection setPosition(int $value)
 * @method int getIsDefault()
 * @method Magento_Bundle_Model_Selection setIsDefault(int $value)
 * @method int getSelectionPriceType()
 * @method Magento_Bundle_Model_Selection setSelectionPriceType(int $value)
 * @method float getSelectionPriceValue()
 * @method Magento_Bundle_Model_Selection setSelectionPriceValue(float $value)
 * @method float getSelectionQty()
 * @method Magento_Bundle_Model_Selection setSelectionQty(float $value)
 * @method int getSelectionCanChangeQty()
 * @method Magento_Bundle_Model_Selection setSelectionCanChangeQty(int $value)
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Model_Selection extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Bundle_Model_Resource_Selection');
        parent::_construct();
    }

    /**
     * Processing object before save data
     *
     * @return Magento_Bundle_Model_Selection
     */
    protected function _beforeSave()
    {
        if (!Mage::helper('Magento_Catalog_Helper_Data')->isPriceGlobal() && $this->getWebsiteId()) {
            $this->getResource()->saveSelectionPrice($this);

            if (!$this->getDefaultPriceScope()) {
                $this->unsSelectionPriceValue();
                $this->unsSelectionPriceType();
            }
        }
        parent::_beforeSave();
    }
}
