<?php
/**
 * Backend block template context
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Backend_Block_Template_Context extends Mage_Core_Block_Template_Context
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_UrlInterface $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_CacheInterface $cache
     * @param Mage_Core_Model_Design_PackageInterface $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Logger $logger
     * @param Magento_Filesystem $filesystem
     * @param Magento_AuthorizationInterface $authorization
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_UrlInterface $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_CacheInterface $cache,
        Mage_Core_Model_Design_PackageInterface $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Magento_Filesystem $filesystem,
        Magento_AuthorizationInterface $authorization
    ) {
        parent::__construct(
            $request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $dirs, $logger, $filesystem
        );
        $this->_authorization = $authorization;
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
