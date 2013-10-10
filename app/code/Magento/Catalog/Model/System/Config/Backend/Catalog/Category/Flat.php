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
 * Flat category on/off backend
 */
namespace Magento\Catalog\Model\System\Config\Backend\Catalog\Category;

class Flat extends \Magento\Core\Model\Config\Value
{
    /**
     * Indexer factory
     *
     * @var \Magento\Index\Model\IndexerFactory
     */
    protected $_indexerFactory;

    /**
     * Construct
     *
     * @param \Magento\Index\Model\IndexerFactory $indexerFactory
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Index\Model\IndexerFactory $indexerFactory,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_indexerFactory = $indexerFactory;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After enable flat category required reindex
     *
     * @return \Magento\Catalog\Model\System\Config\Backend\Catalog\Category\Flat
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            $this->_indexerFactory->create()
                ->getProcessByCode(\Magento\Catalog\Helper\Category\Flat::CATALOG_CATEGORY_FLAT_PROCESS_CODE)
                ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
