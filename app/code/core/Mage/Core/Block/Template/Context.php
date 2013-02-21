<?php
/**
 * Magento block context object. Contains all block dependencies. Should not be used by any other class
 *
 * {licence_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Block_Template_Context extends Mage_Core_Block_Context
{
    /**
     * Dirs instance
     *
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Logger instance
     *
     * @var Mage_Core_Model_Logger
     */
    protected $_logger;

    /**
     * Filesystem instance
     *
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Logger $logger
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Magento_Filesystem $filesystem
    ) {
        parent::__construct(
            $request, $layout, $eventManager, $urlBuilder, $translator, $cache,
            $designPackage, $session, $storeConfig, $frontController, $helperFactory
        );

        $this->_dirs = $dirs;
        $this->_logger = $logger;
        $this->_filesystem = $filesystem;
    }

    /**
     * @return Mage_Core_Model_Dir
     */
    public function getDirs()
    {
        return $this->_dirs;
    }

    public function getFilesystem()
    {
        return $this->_filesystem;
    }

    public function getLogger()
    {
        return $this->_logger;
    }
}
