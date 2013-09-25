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
 * Catalog product country attribute source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Source_Countryofmanufacture
    extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var Magento_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Country factory
     *
     * @var Magento_Directory_Model_CountryFactory
     */
    protected $_countryFactory;

    /**
     * Construct
     *
     * @param Magento_Directory_Model_CountryFactory $countryFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     */
    public function __construct(
        Magento_Directory_Model_CountryFactory $countryFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Cache_Type_Config $configCacheType
    ) {
        $this->_countryFactory = $countryFactory;
        $this->_storeManager = $storeManager;
        $this->_configCacheType = $configCacheType;
    }

    /**
     * Get list of all available countries
     *
     * @return mixed
     */
    public function getAllOptions()
    {
        $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_' . $this->_storeManager->getStore()->getCode();
        if ($cache = $this->_configCacheType->load($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $collection = $this->_countryFactory->create()->getResourceCollection()
                ->loadByStore();
            $options = $collection->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        return $options;
    }
}
