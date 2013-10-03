<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\System\Config\Backend;

class Active
    extends \Magento\Backend\Model\Config\Backend\Cache
    implements \Magento\Backend\Model\Config\CommentInterface
{
    /**
     * @var \Magento\SalesArchive\Model\Archive
     */
    protected $_archive;

    /**
     * @var \Magento\SalesArchive\Model\Resource\Order\Collection
     */
    protected $_orderCollection;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\SalesArchive\Model\Archive $archive
     * @param \Magento\SalesArchive\Model\Resource\Order\Collection $orderCollection
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\SalesArchive\Model\Archive $archive,
        \Magento\SalesArchive\Model\Resource\Order\Collection $orderCollection,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_archive = $archive;
        $this->_orderCollection = $orderCollection;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(
        \Magento\Backend\Block\Menu::CACHE_TAGS
    );

    /**
     * Clean cache, value was changed
     *
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->isValueChanged() && !$this->getValue()) {
            $this->_archive->removeOrdersFromArchive();
        }
        return $this;
    }

    /**
     * Get field comment
     *
     * @param string $currentValue
     * @return string
     */
    public function getCommentText($currentValue)
    {
        if ($currentValue) {
            $ordersCount = $this->_orderCollection->getSize();
            if ($ordersCount) {
                return __('There are %1 orders in this archive. All of them will be moved to the regular table after the archive is disabled.', $ordersCount);
            }
        }
        return '';
    }
}
