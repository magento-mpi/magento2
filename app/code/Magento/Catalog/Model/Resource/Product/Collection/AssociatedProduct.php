<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Collection;

/**
 * Catalog compare item resource model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AssociatedProduct
    extends \Magento\Catalog\Model\Resource\Product\Collection
{
    /**
     * Registry instance
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;

    /**
     * Product type configurable instance
     *
     * @var \Magento\Catalog\Model\Product\Type\Configurable
     */
    protected $_productType;

    /**
     * Configuration helper instance
     *
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $_configurationHelper;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\App\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\Resource\Helper $resourceHelper
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Catalog\Helper\Product\Flat $catalogProductFlat
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\Resource\Url $catalogUrl
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Registry $registryManager
     * @param \Magento\Catalog\Model\Product\Type\Configurable $productType
     * @param \Magento\Catalog\Helper\Product\Configuration $configurationHelper
     * @param \Zend_Db_Adapter_Abstract $connection
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\App\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\Resource\Helper $resourceHelper,
        \Magento\Validator\UniversalFactory $universalFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Helper\Product\Flat $catalogProductFlat,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\Resource\Url $catalogUrl,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Core\Model\Registry $registryManager,
        \Magento\Catalog\Model\Product\Type\Configurable $productType,
        \Magento\Catalog\Helper\Product\Configuration $configurationHelper,
        $connection = null
    ) {
        $this->_registryManager = $registryManager;
        $this->_productType = $productType;
        $this->_configurationHelper = $configurationHelper;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $catalogData,
            $catalogProductFlat,
            $coreStoreConfig,
            $productOptionFactory,
            $catalogUrl,
            $locale,
            $customerSession,
            $dateTime,
            $connection
        );
    }

    /**
     * Get product type
     *
     * @return \Magento\Catalog\Model\Product\Type\Configurable
     */
    public function getProductType()
    {
        return $this->_productType;
    }

    /**
     * Retrieve currently edited product object
     *
     * @return mixed
     */
    private function getProduct()
    {
        return $this->_registryManager->registry('current_product');
    }

    /**
     * Add attributes to select
     *
     * @return $this
     */
    public function _initSelect()
    {
        parent::_initSelect();

        $allowedProductTypes = $this->_configurationHelper->getConfigurableAllowedTypes();

        $this->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('weight')
            ->addAttributeToSelect('image')
            ->addFieldToFilter('type_id', $allowedProductTypes)
            ->addFieldToFilter('entity_id', array('neq' => $this->getProduct()->getId()))
            ->addFilterByRequiredOptions()
            ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner');

        return $this;
    }
}
