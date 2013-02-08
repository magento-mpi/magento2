<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PageCache_Model_RequestProcessor_Maintenance implements Enterprise_PageCache_Model_RequestProcessorInterface
{
    /**
     * @var Saas_Saas_Model_Maintenance_Config
     */
    protected $_maintenanceConfig;

    /**
     * @param Saas_Saas_Model_Maintenance_Config $maintenanceConfig
     */
    public function __construct(Saas_Saas_Model_Maintenance_Config $maintenanceConfig)
    {
        $this->_maintenanceConfig = $maintenanceConfig;
    }

    /**
     * Removing redirect to maintenance page
     *
     * @param Zend_Controller_Response_Http $response
     */
    protected function _removeMaintenanceHeader(Zend_Controller_Response_Http $response)
    {
        $headers = $response->getHeaders();
        foreach ($headers as $header) {
            if (false === is_array($header)) {
                continue;
            }

            if (!empty($header['name']) && !empty($header['value']) && !empty($header['replace'])) {
                if ($header['name'] == 'Location'
                    && false !== strpos($header['value'], 'maintenance')
                    && true == $header['replace']
                ) {
                    $response->clearHeader('Location');
                }
            }
        }
    }

    /**
     * Apply custom logic after content extraction
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     * @param bool|string $content
     * @return bool|string
     */
    public function extractContent(
        Zend_Controller_Request_Http $request,
        Zend_Controller_Response_Http $response,
        $content
    ) {
        /**
         * Removing redirect to maintenance page
         */
        $this->_removeMaintenanceHeader($response);

        /** If we have content - we are at the frontend for sure */
        if ($content) {
            if ($this->_maintenanceConfig->isMaintenanceMode() && $this->_maintenanceConfig->getUrl()) {
                $inWhiteList = in_array($request->getServer('REMOTE_ADDR'), $this->_maintenanceConfig->getWhiteList());
                if (false == $inWhiteList && (strpos($request->getServer('REQUEST_URI'), 'maintenance') === false)) {
                    $response->clearAllHeaders();
                    $response->setRedirect($this->_maintenanceConfig->getUrl());
                }
            }
        }

        return $content;
    }
}
