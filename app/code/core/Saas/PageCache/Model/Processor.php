<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PageCache_Model_Processor extends Enterprise_PageCache_Model_Processor
{
    /**
     * @var Saas_Saas_Model_Maintenance_Config
     */
    protected $_maintenanceConfig;

    /**
     * @param string $scopeCode
     * @param Enterprise_PageCache_Model_Processor_RestrictionInterface $restriction
     * @param Enterprise_PageCache_Model_Cache $fpcCache
     * @param Mage_Core_Model_Design_Package_Proxy $designPackage
     * @param Enterprise_PageCache_Model_Cache_SubProcessorFactory $subProcessorFactory
     * @param Enterprise_PageCache_Model_Container_PlaceholderFactory $placeholderFactory
     * @param Enterprise_PageCache_Model_ContainerFactory $containerFactory
     * @param Enterprise_PageCache_Model_Environment $environment
     * @param Saas_Saas_Model_Maintenance_Config $maintenanceConfig
     */
    public function __construct(
        $scopeCode,
        Enterprise_PageCache_Model_Processor_RestrictionInterface $restriction,
        Enterprise_PageCache_Model_Cache $fpcCache,
        Mage_Core_Model_Design_Package_Proxy $designPackage,
        Enterprise_PageCache_Model_Cache_SubProcessorFactory $subProcessorFactory,
        Enterprise_PageCache_Model_Container_PlaceholderFactory $placeholderFactory,
        Enterprise_PageCache_Model_ContainerFactory $containerFactory,
        Enterprise_PageCache_Model_Environment $environment,
        Saas_Saas_Model_Maintenance_Config $maintenanceConfig
    ) {
        $this->_maintenanceConfig = $maintenanceConfig;

        parent::__construct($scopeCode, $restriction, $fpcCache, $designPackage, $subProcessorFactory,
            $placeholderFactory, $containerFactory, $environment
        );
    }


    /**
     * Apply custom logic before content extraction
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     * @param bool|string $content
     * @return bool|string
     */
    protected function _beforeExtractContent(
        Zend_Controller_Request_Http $request,
        Zend_Controller_Response_Http $response,
        $content
    ) {
        return parent::_beforeExtractContent($request, $response, $content);
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
                    && (strpos($header['value'], 'maintenance') !== false)
                    && $header['replace'] == true
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
    protected function _afterExtractContent(
        Zend_Controller_Request_Http $request,
        Zend_Controller_Response_Http $response,
        $content
    ) {
        $content = parent::_afterExtractContent($request, $response, $content);

        /**
         * Removing redirect to maintenance page
         * self::extractContent can set it based on the cached headers
         */
        $this->_removeMaintenanceHeader($response);

        /** If we have content - we are at the frontend for sure */
        if ($content) {
            if ($this->_maintenanceConfig->isMaintenanceMode() && $this->_maintenanceConfig->getUrl()) {
                $inWhiteList = in_array($request->getServer('REMOTE_ADDR'), $this->_maintenanceConfig->getWhiteList());
                if (false == $inWhiteList && (strpos($request->getServer('REQUEST_URI'), 'maintenance') === false)) {
                    $response->clearAllHeaders()->setRedirect($this->_maintenanceConfig->getUrl());
                }
            }
        }

        return $content;
    }
}
