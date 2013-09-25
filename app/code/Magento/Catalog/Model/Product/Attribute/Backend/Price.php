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
     * @var Magento_Catalog_Helper_Data
     */
    protected $_helper;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Currency factory
     *
     * @var Magento_Directory_Model_CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * Core config model
     *
     * @var Magento_Core_Model_ConfigInterface
     */
    protected $_config;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Directory_Model_CurrencyFactory $currencyFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Model_Config $config
    ) {
        $this->_currencyFactory = $currencyFactory;
        $this->_storeManager = $storeManager;
        $this->_helper = $catalogData;
        $this->_config = $config;
        parent::__construct($logger);
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
            $baseCurrency = $this->_config->getValue(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_BASE,
                'default');

            $storeIds = $object->getStoreIds();
            if (is_array($storeIds)) {
                foreach ($storeIds as $storeId) {
                    $storeCurrency = $this->_storeManager->getStore($storeId)->getBaseCurrencyCode();
                    if ($storeCurrency == $baseCurrency) {
                        continue;
                    }
                    $rate = $this->_currencyFactory->create()->load($baseCurrency)->getRate($storeCurrency);
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
            throw new Magento_Core_Exception(
                __('Please enter a number 0 or greater in this field.')
            );
        }

        return true;
    }
}
