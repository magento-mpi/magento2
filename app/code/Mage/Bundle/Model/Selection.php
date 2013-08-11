<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle Selection Model
 *
 * @method Mage_Bundle_Model_Resource_Selection _getResource()
 * @method Mage_Bundle_Model_Resource_Selection getResource()
 * @method int getOptionId()
 * @method Mage_Bundle_Model_Selection setOptionId(int $value)
 * @method int getParentProductId()
 * @method Mage_Bundle_Model_Selection setParentProductId(int $value)
 * @method int getProductId()
 * @method Mage_Bundle_Model_Selection setProductId(int $value)
 * @method int getPosition()
 * @method Mage_Bundle_Model_Selection setPosition(int $value)
 * @method int getIsDefault()
 * @method Mage_Bundle_Model_Selection setIsDefault(int $value)
 * @method int getSelectionPriceType()
 * @method Mage_Bundle_Model_Selection setSelectionPriceType(int $value)
 * @method float getSelectionPriceValue()
 * @method Mage_Bundle_Model_Selection setSelectionPriceValue(float $value)
 * @method float getSelectionQty()
 * @method Mage_Bundle_Model_Selection setSelectionQty(float $value)
 * @method int getSelectionCanChangeQty()
 * @method Mage_Bundle_Model_Selection setSelectionCanChangeQty(int $value)
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Selection extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Mage_Bundle_Model_Resource_Selection');
        parent::_construct();
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Bundle_Model_Selection
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
