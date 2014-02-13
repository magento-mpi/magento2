<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Model\Resource;

/**
 * Resource Setup Model
 */
class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @var  \Magento\Integration\Model\Manager
     */
    protected $_integrationManager;

    /**
     * Construct resource Setup Model
     *
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\Integration\Model\Manager $integrationManager
     * @param string $moduleName
     * @param string $connectionName
     *
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        $resourceName,
        \Magento\Integration\Model\Manager $integrationManager,
        $moduleName = 'Magento_Integration',
        $connectionName = ''
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
