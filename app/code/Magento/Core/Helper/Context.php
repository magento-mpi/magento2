<?php
/**
 * Abstract helper context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Helper_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @var Magento_Core_Model_ModuleManager
     */
    protected $_moduleManager;

    /**
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * @var Magento_Core_Model_UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var Magento_Core_Model_Url
     */
    protected $_urlModel;

    /**
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_ModuleManager $moduleManager
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_UrlFactory $urlFactory
     * @param Magento_Core_Model_Url_Proxy $urlModel
     */
    public function __construct(
        Magento_Core_Model_Translate $translator,
        Magento_Core_Model_ModuleManager $moduleManager,
        Magento_Core_Model_App $app,
        Magento_Core_Model_UrlFactory $urlFactory,
        Magento_Core_Model_Url_Proxy $urlModel
    ) {
        $this->_translator = $translator;
        $this->_moduleManager = $moduleManager;
        $this->_app = $app;
        $this->_urlFactory = $urlFactory;
        $this->_urlModel = $urlModel;
    }

    /**
     * @return Magento_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * @return Magento_Core_Model_ModuleManager
     */
    public function getModuleManager()
    {
        return $this->_moduleManager;
    }

    /**
     * @return Magento_Core_Model_App
     */
    public function getApp()
    {
        return $this->_app;
    }

    /**
     * @return Magento_Core_Model_UrlFactory
     */
    public function getUrlFactory()
    {
        return $this->_urlFactory;
    }

    /**
     * @return Magento_Core_Model_Url
     */
    public function getUrlModel()
    {
        return $this->_urlModel;
    }
}
