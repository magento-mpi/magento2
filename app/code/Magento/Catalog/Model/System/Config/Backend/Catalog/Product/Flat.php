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
 * Flat product on/off backend
 */
namespace Magento\Catalog\Model\System\Config\Backend\Catalog\Product;

class Flat extends \Magento\Core\Model\Config\Value
{
    /**
     * Index indexer
     *
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexIndexer;

    /**
     * Construct
     *
     * @param \Magento\Index\Model\Indexer $indexIndexer
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Index\Model\Indexer $indexIndexer,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_indexIndexer = $indexIndexer;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After enable flat products required reindex
     *
     * @return \Magento\Catalog\Model\System\Config\Backend\Catalog\Product\Flat
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            $this->_indexIndexer->getProcessByCode('catalog_product_flat')
                ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
