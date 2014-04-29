<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Resource;

use Magento\Integration\Model\Manager;

/**
 * Resource Setup Model
 */
class Setup extends \Magento\Framework\Module\Setup
{
    /**
     * @var  Manager
     */
    protected $_integrationManager;

    /**
     * Construct resource Setup Model
     *
     * @param \Magento\Framework\Module\Setup\Context $context
     * @param string $resourceName
     * @param Manager $integrationManager
     * @param string $moduleName
     * @param string $connectionName
     *
     */
    public function __construct(
        \Magento\Framework\Module\Setup\Context $context,
        $resourceName,
        Manager $integrationManager,
        $moduleName = 'Magento_Integration',
        $connectionName = \Magento\Framework\Module\Updater\SetupInterface::DEFAULT_SETUP_CONNECTION
    ) {
        $this->_integrationManager = $integrationManager;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * Initiate integration processing
     *
     * @param array $integrationNames
     * @return array of integration names sent to the next invocation
     */
    public function initIntegrationProcessing(array $integrationNames)
    {
        $this->_integrationManager->processIntegrationConfig($integrationNames);
        return $integrationNames;
    }
}
