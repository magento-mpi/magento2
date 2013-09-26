<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Inventory Manage Stock Config Backend Model
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model\Config\Backend;

class Managestock
    extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status
     */
    protected $_stockStatus;

    /**
     * @param \Magento\CatalogInventory\Model\Stock\Status $stockStatus
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\CatalogInventory\Model\Stock\Status $stockStatus,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_stockStatus = $stockStatus;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After change Catalog Inventory Manage value process
     *
     * @return \Magento\CatalogInventory\Model\Config\Backend\Managestock
     */
    protected function _afterSave()
    {
        $oldValue = $this->_config->getValue(
            \Magento\CatalogSearch\Model\Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($this->getValue() != $oldValue) {
            $this->_stockStatus->rebuild();
        }

        return $this;
    }
}
