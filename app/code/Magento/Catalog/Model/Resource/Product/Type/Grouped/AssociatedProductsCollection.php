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
 * Associated products collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Product\Type\Grouped;

class AssociatedProductsCollection
    extends \Magento\Catalog\Model\Resource\Product\Link\Product\Collection
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Catalog\Helper\Product\Flat $catalogProductFlat
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Helper\Product\Flat $catalogProductFlat,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $config,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_config = $config;
        parent::__construct($catalogData, $catalogProductFlat, 
            $eventManager, $logger, $fetchStrategy, $coreStoreConfig, $entityFactory
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

        $configData = $this->_config->getType('grouped');
        $allowProductTypes = isset($configData['allow_product_types']) ? $configData['allow_product_types'] : array();
        $this->setProduct($this->_getProduct())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('sku')
            ->addFilterByRequiredOptions()
            ->addAttributeToFilter('type_id', $allowProductTypes);

        return $this;
    }
}
