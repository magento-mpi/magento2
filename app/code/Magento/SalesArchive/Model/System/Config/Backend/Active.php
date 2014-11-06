<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\System\Config\Backend;

class Active extends \Magento\Backend\Model\Config\Backend\Cache implements
    \Magento\Backend\Model\Config\CommentInterface,
    \Magento\Framework\Object\IdentityInterface
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
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\SalesArchive\Model\Archive $archive
     * @param \Magento\SalesArchive\Model\Resource\Order\Collection $orderCollection
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\SalesArchive\Model\Archive $archive,
        \Magento\SalesArchive\Model\Resource\Order\Collection $orderCollection,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_archive = $archive;
        $this->_orderCollection = $orderCollection;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(\Magento\Backend\Block\Menu::CACHE_TAGS);

    /**
     * Clean cache, value was changed
     *
     * @return $this
     */
    public function afterSave()
    {
        parent::afterSave();
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
                return __(
                    'There are %1 orders in this archive. All of them will be moved to the regular table after the archive is disabled.',
                    $ordersCount
                );
            }
        }
        return '';
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return array(\Magento\Backend\Block\Menu::CACHE_TAGS);
    }
}
