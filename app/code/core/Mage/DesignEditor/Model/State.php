<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design editor state model
 */
class Mage_DesignEditor_Model_State
{
    /**#@+
     * Name of layout classes that will be used as main layout
     */
    const LAYOUT_DESIGN_CLASS_NAME     = 'Mage_DesignEditor_Model_Layout';
    const LAYOUT_NAVIGATION_CLASS_NAME = 'Mage_Core_Model_Layout';
    /**#@-*/

    /**#@+
     * Url model classes that will be used instead of Mage_Core_Model_Url in different vde modes
     */
    const URL_MODEL_NAVIGATION_MODE_CLASS_NAME = 'Mage_DesignEditor_Model_Url_NavigationMode';
    const URL_MODEL_DESIGN_MODE_CLASS_NAME     = 'Mage_DesignEditor_Model_Url_DesignMode';
    /**#@-*/

    /**#@+
     * Layout update resource models
     */
    const LAYOUT_UPDATE_RESOURCE_MODEL_CORE_CLASS_NAME = 'Mage_Core_Model_Resource_Layout_Update';
    const LAYOUT_UPDATE_RESOURCE_MODEL_VDE_CLASS_NAME  = 'Mage_DesignEditor_Model_Resource_Layout_Update';
    /**#@-*/

    /**#@+
     * Import behaviors
     */
    const MODE_DESIGN     = 'design';
    const MODE_NAVIGATION = 'navigation';
    /**#@-*/

    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @var Mage_Core_Model_Layout_Factory
     */
    protected $_layoutFactory;

    /**
     * @var Mage_DesignEditor_Model_Url_Factory
     */
    protected $_urlModelFactory;

    /**
     * Application Cache Manager
     *
     * @var Mage_Core_Model_Cache
     */
    protected $_cacheManager;

    /**
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_dataHelper;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Mage_Backend_Model_Session $backendSession
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_DesignEditor_Model_Url_Factory $urlModelFactory
     * @param Mage_Core_Model_Cache $cacheManager
     * @param Mage_DesignEditor_Helper_Data $dataHelper
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Mage_Backend_Model_Session $backendSession,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_DesignEditor_Model_Url_Factory $urlModelFactory,
        Mage_Core_Model_Cache $cacheManager,
        Mage_DesignEditor_Helper_Data $dataHelper,
        Magento_ObjectManager $objectManager
    ) {
        $this->_backendSession  = $backendSession;
        $this->_layoutFactory   = $layoutFactory;
        $this->_urlModelFactory = $urlModelFactory;
        $this->_cacheManager    = $cacheManager;
        $this->_dataHelper      = $dataHelper;
        $this->_objectManager   = $objectManager;
    }

    public function update(
        $areaCode,
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Varien_ActionAbstract $controller
    ) {
        $handle = $request->getParam('handle', '');
        if (empty($handle)) {
            $mode = self::MODE_NAVIGATION;

            if (!$request->isAjax()) {
                $this->_backendSession->setData('vde_current_handle', $controller->getFullActionName());
                $this->_backendSession->setData('vde_current_url', $request->getPathInfo());
            }
        } else {
            $mode = self::MODE_DESIGN;
        }

        $this->_backendSession->setData('vde_current_mode', $mode);
        $this->_injectUrlModel($mode);
        $this->_injectLayout($mode, $areaCode);
        $this->_injectLayoutUpdateResourceModel();
        $this->_disableCache();
    }

    /**
     * Create layout instance that will be used as main layout for whole system
     *
     * @param string $mode
     * @param string $areaCode
     */
    protected function _injectLayout($mode, $areaCode)
    {
        switch ($mode) {
            case self::MODE_DESIGN:
                $this->_layoutFactory->createLayout(array('area' => $areaCode), self::LAYOUT_DESIGN_CLASS_NAME);
                break;
            case self::MODE_NAVIGATION:
            default:
                $this->_layoutFactory->createLayout(array('area' => $areaCode), self::LAYOUT_NAVIGATION_CLASS_NAME);
                break;
        }
    }

    /**
     * Create url model instance that will be used instead of Mage_Core_Model_Url in navigation mode
     */
    protected function _injectUrlModel($mode)
    {
        switch ($mode) {
            case self::MODE_DESIGN:
                $this->_urlModelFactory->replaceClassName(self::URL_MODEL_DESIGN_MODE_CLASS_NAME);
                break;
            case self::MODE_NAVIGATION:
            default:
                $this->_urlModelFactory->replaceClassName(self::URL_MODEL_NAVIGATION_MODE_CLASS_NAME);
                break;
        }
    }

    /**
     * Replace layout update resource model with custom vde one
     */
    protected function _injectLayoutUpdateResourceModel()
    {
        $this->_objectManager->addAlias(self::LAYOUT_UPDATE_RESOURCE_MODEL_CORE_CLASS_NAME,
            self::LAYOUT_UPDATE_RESOURCE_MODEL_VDE_CLASS_NAME
        );
    }

    /**
     * Disable some cache types in VDE mode
     */
    protected function _disableCache()
    {
        foreach ($this->_dataHelper->getDisabledCacheTypes() as $cacheCode) {
            $this->_cacheManager->banUse($cacheCode);
            $this->_cacheManager->cleanType($cacheCode);
        }
    }
}
