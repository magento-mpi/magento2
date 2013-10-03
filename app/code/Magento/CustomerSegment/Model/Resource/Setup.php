<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CustomerSegment resource setup
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Model\Resource;

class Setup extends \Magento\Eav\Model\Entity\Setup
{
    /**
     * @var \Magento\Enterprise\Model\Resource\Setup\MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory
     * @param \Magento\Enterprise\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory $collectionFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory,
        \Magento\Enterprise\Model\Resource\Setup\MigrationFactory $migrationFactory,
        \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory $collectionFactory,
        $resourceName,
        $moduleName = 'Magento_CustomerSegment',
        $connectionName = ''
    ) {
        parent::__construct($context, $cache, $attrGrCollFactory, $resourceName, $moduleName, $connectionName);
        $this->_migrationFactory = $migrationFactory;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @param array $data
     * @return \Magento\Enterprise\Model\Resource\Setup\Migration
     */
    public function createSetupMigration(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }

    /**
     * @return \Magento\CustomerSegment\Model\Resource\Segment\Collection
     */
    public function createSegmentCollection()
    {
        return $this->_collectionFactory->create();
    }
}
