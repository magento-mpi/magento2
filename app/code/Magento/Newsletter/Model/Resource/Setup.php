<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter resource setup
 */
namespace Magento\Newsletter\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * Resource setup model
     *
     * @var \Magento\Core\Model\Resource\Setup\Migration
     */
    protected $_setupMigration;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $setupMigrationFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Core\Model\Resource\Setup\MigrationFactory $setupMigrationFactory,
        $resourceName,
        $moduleName = 'Magento_Newsletter',
        $connectionName = ''
    ) {
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
        $this->_setupMigration = $setupMigrationFactory->create(
            array('resourceName' => 'core_setup')
        );
    }


    /**
     * Get block factory
     *
     * @return \Magento\Core\Model\Resource\Setup\Migration
     */
    public function getSetupMigration()
    {
        return $this->_setupMigration;
    }
}
