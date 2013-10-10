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
namespace Magento\Catalog\Model\Product\Attribute\Source;

class Countryofmanufacture
    extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Country factory
     *
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * Construct
     *
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     */
    public function __construct(
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Cache\Type\Config $configCacheType
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
