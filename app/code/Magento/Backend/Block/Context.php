<?php
/**
 * Backend block context
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Backend_Block_Context extends Magento_Core_Block_Context
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_Session_Abstract $session
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Core_Controller_Varien_Front $frontController
     * @param Magento_Core_Model_Factory_Helper $helperFactory
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Core_Model_View_Config $viewConfig
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param Magento_AuthorizationInterface $authorization
     * @param Magento_Core_Model_Logger $logger
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_Core_Model_Translate $translator,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_Session_Abstract $session,
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Core_Controller_Varien_Front $frontController,
        Magento_Core_Model_Factory_Helper $helperFactory,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Core_Model_View_Config $viewConfig,
        Magento_Core_Model_Cache_StateInterface $cacheState,
        Magento_AuthorizationInterface $authorization,
        Magento_Core_Model_Logger $logger,
        array $data = array()
    ) {
        $this->_authorization = $authorization;
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $design,
            $session, $storeConfig, $frontController, $helperFactory, $viewUrl, $viewConfig, $cacheState, $logger, $data
        );
    }

    /**
     * Retrieve Authorization
     *
     * @return \Magento_AuthorizationInterface
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }
}
