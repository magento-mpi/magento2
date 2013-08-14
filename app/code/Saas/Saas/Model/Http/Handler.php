<?php
/**
 * SaaS HTTP handler
 *
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Saas
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_Http_Handler implements Magento_HTTP_HandlerInterface
{
    /**
     * @var Magento_Core_Model_Config_Primary
     */
    protected $_config;

    /**
     * @var Saas_Saas_Model_Maintenance_Config
     */
    protected $_maintenanceConfig;

    /**
     * @param Magento_Core_Model_Config_Primary $config
     * @param Saas_Saas_Model_Maintenance_Config $maintenanceConfig
     */
    public function __construct(
        Magento_Core_Model_Config_Primary $config,
        Saas_Saas_Model_Maintenance_Config $maintenanceConfig
    ) {
        $this->_config = $config;
        $this->_maintenanceConfig = $maintenanceConfig;
    }

    /**
     * Handle http request
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     */
    public function handle(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
    {
        if ($this->_config->getParam('status') == Saas_Saas_Model_Tenant_Config::STATUS_DISABLED_FRONTEND) {
            $path = explode('/', ltrim($request->getPathInfo(), '/'));
            if ($path[0] !== (string)$this->_config->getNode('global/areas/adminhtml/frontName')) {
                $response->setRedirect($this->_maintenanceConfig->getUrl());
                $response->sendResponse();
                $request->setDispatched(true);
            }
        }
    }
}
