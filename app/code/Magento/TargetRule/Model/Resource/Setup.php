<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Setup Resource Model
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\TargetRule\Model\Resource;

class Setup extends \Magento\Catalog\Model\Resource\Setup
{
    /**
     * @var \Magento\Enterprise\Model\Resource\Setup\MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @param \Magento\Enterprise\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Enterprise\Model\Resource\Setup\MigrationFactory $migrationFactory,
        $resourceName,
        $moduleName = 'Magento_TargetRule',
        $connectionName = ''
    ) {
        $this->_migrationFactory = $migrationFactory;
        parent::__construct($context, $cache, $resourceName, $moduleName, $connectionName);
    }


    /**
     * Create migration setup
     *
     * @param array $data
     * @return \Magento\Enterprise\Model\Resource\Setup\Migration
     */
    public function createMigrationSetup(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }
}
