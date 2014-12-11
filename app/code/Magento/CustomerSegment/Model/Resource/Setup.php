<?php
/**
 * CustomerSegment resource setup
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Resource;

class Setup extends \Magento\Eav\Model\Entity\Setup
{
    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Eav\Model\Entity\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory $collectionFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Setup\Context $context,
        $resourceName,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory $collectionFactory,
        $moduleName = 'Magento_CustomerSegment',
        $connectionName = \Magento\Framework\Module\Updater\SetupInterface::DEFAULT_SETUP_CONNECTION
    ) {
        parent::__construct(
            $context,
            $resourceName,
            $cache,
            $attrGroupCollectionFactory,
            $moduleName,
            $connectionName
        );
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\CustomerSegment\Model\Resource\Segment\Collection
     */
    public function createSegmentCollection()
    {
        return $this->_collectionFactory->create();
    }
}
