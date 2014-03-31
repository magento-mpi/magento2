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

class Setup extends \Magento\Module\Setup
{
    /**
     * Resource setup model
     *
     * @var \Magento\Module\Setup\Migration
     */
    protected $_setupMigration;

    /**
     * @param \Magento\Module\Setup\Context $context
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Module\Setup\Context $context,
        $resourceName,
        $moduleName = 'Magento_Newsletter',
        $connectionName = ''
    ) {
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
        $this->_setupMigration = $this->_migrationFactory->create(array('resourceName' => 'core_setup'));
    }

    /**
     * Get block factory
     *
     * @return \Magento\Module\Setup\Migration
     */
    public function getSetupMigration()
    {
        return $this->_migrationFactory->create(array('resourceName' => 'core_setup'));
    }
}
