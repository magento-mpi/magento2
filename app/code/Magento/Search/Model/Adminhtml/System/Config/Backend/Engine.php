<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search backend model
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Model\Adminhtml\System\Config\Backend;

class Engine extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    /**
     * @param \Magento\Index\Model\Indexer $indexer
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Index\Model\Indexer $indexer,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_indexer = $indexer;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After save call
     * Invalidate catalog search index if engine was changed
     *
     * @return \Magento\Search\Model\Adminhtml\System\Config\Backend\Engine
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if ($this->isValueChanged()) {
            $this->_indexer->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
