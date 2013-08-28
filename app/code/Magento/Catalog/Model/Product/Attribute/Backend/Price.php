<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product price attribute backend model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Price extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Catalog helper
     *
     * @var Magento_Core_Helper_String
     */
    protected $_helper;

    /**
     * @param Magento_Core_Helper_String $coreString
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_String $coreString,
        $data = array()
    ) {
        $this->_helper = $coreString;
    }

    /**
     * Set Attribute instance
     * Rewrite for redefine attribute scope
     *
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Magento_Catalog_Model_Product_Attribute_Backend_Price
     */
    public function setAttribute($attribute)
    {
        parent::setAttribute($attribute);
        $this->setScope($attribute);
        return $this;
    }

    /**
     * Redefine Attribute scope
     *
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Magento_Catalog_Model_Product_Attribute_Backend_Price
     */
    public function setScope($attribute)
    {
        if ($this->_helper->isPriceGlobal()) {
            $attribute->setIsGlobal(Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL);
        } else {
            $attribute->setIsGlobal(Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE);
        }

        return $this;
    }

    /**
     * After Save Attribute manipulation
     *
     * @param Magento_Catalog_Model_Product $object
     * @return Magento_Catalog_Model_Product_Attribute_Backend_Price
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        /**
         * Orig value is only for existing objects
         */
        $oridData = $object->getOrigData();
        $origValueExist = $oridData && array_key_exists($this->getAttribute()->getAttributeCode(), $oridData);
        if ($object->getStoreId() != 0 || !$value || $origValueExist) {
            return $this;
        }

        if ($this->getAttribute()->getIsGlobal() == Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE) {
            $baseCurrency = Mage::app()->getBaseCurrencyCode();

            $storeIds = $object->getStoreIds();
            if (is_array($storeIds)) {
                foreach ($storeIds as $storeId) {
                    $storeCurrency = Mage::app()->getStore($storeId)->getBaseCurrencyCode();
                    if ($storeCurrency == $baseCurrency) {
                        continue;
                    }
                    $rate = Mage::getModel('Magento_Directory_Model_Currency')->load($baseCurrency)->getRate($storeCurrency);
                    if (!$rate) {
                        $rate = 1;
                    }
                    $newValue = $value * $rate;
                    $object->addAttributeUpdate($this->getAttribute()->getAttributeCode(), $newValue, $storeId);
                }
            }
        }

        return $this;
    }

    /**
     * Validate
     *
     * @param Magento_Catalog_Model_Product $object
     * @throws Magento_Core_Exception
     * @return bool
     */
    public function validate($object)
    {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        if (empty($value)) {
            return parent::validate($object);
        }

        if (!preg_match('/^\d*(\.|,)?\d{0,4}$/i', $value) || $value < 0) {
            Mage::throwException(
                __('Please enter a number 0 or greater in this field.')
            );
        }

        return true;
    }
}
