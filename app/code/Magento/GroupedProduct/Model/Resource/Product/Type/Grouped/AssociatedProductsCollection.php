<?php
/**
 * Associated products collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GroupedProduct\Model\Resource\Product\Type\Grouped;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AssociatedProductsCollection extends \Magento\Catalog\Model\Resource\Product\Link\Product\Collection
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Product types config
     *
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\Resource\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\Resource\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     * @param mixed $connection
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\Resource\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\Resource\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $config,
        $connection = null
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_config = $config;
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
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $connection
        );
    }

    /**
     * Retrieve currently edited product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function _getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * @inheritdoc
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->setProduct(
            $this->_getProduct()
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'price'
        )->addAttributeToSelect(
            'sku'
        )->addFilterByRequiredOptions()->addAttributeToFilter(
            'type_id',
            $this->_config->getComposableTypes()
        );

        return $this;
    }
}
