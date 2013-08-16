<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design editor state model
 */
class Magento_DesignEditor_Model_State
{
    /**
     * Name of layout classes that will be used as main layout
     */
    const LAYOUT_NAVIGATION_CLASS_NAME = 'Magento_Core_Model_Layout';

    /**
     * Url model classes that will be used instead of Magento_Core_Model_Url in navigation vde modes
     */
    const URL_MODEL_NAVIGATION_MODE_CLASS_NAME = 'Magento_DesignEditor_Model_Url_NavigationMode';

    /**
     * Import behaviors
     */
    const MODE_NAVIGATION = 'navigation';

    /**#@+
     * Session keys
     */
    const CURRENT_URL_SESSION_KEY    = 'vde_current_url';
    const CURRENT_MODE_SESSION_KEY   = 'vde_current_mode';
    /**#@-*/

    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @var Magento_Core_Model_Layout_Factory
     */
    protected $_layoutFactory;

    /**
     * @var Magento_DesignEditor_Model_Url_Factory
     */
    protected $_urlModelFactory;

    /**
     * Application Cache Manager
     *
     * @var Magento_Core_Model_Cache_StateInterface
     */
    protected $_cacheState;

    /**
     * @var Magento_DesignEditor_Helper_Data
     */
    protected $_dataHelper;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_App
     */
    protected $_application;

    /**
     * @param Magento_Backend_Model_Session $backendSession
     * @param Magento_Core_Model_Layout_Factory $layoutFactory
     * @param Magento_DesignEditor_Model_Url_Factory $urlModelFactory
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param Magento_DesignEditor_Helper_Data $dataHelper
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_App $application
     * @param Magento_DesignEditor_Model_Theme_Context $themeContext
     */
    public function __construct(
        Magento_Backend_Model_Session $backendSession,
        Magento_Core_Model_Layout_Factory $layoutFactory,
        Magento_DesignEditor_Model_Url_Factory $urlModelFactory,
        Magento_Core_Model_Cache_StateInterface $cacheState,
        Magento_DesignEditor_Helper_Data $dataHelper,
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_App $application,
        Magento_DesignEditor_Model_Theme_Context $themeContext
    ) {
        $this->_backendSession  = $backendSession;
        $this->_layoutFactory   = $layoutFactory;
        $this->_urlModelFactory = $urlModelFactory;
        $this->_cacheState      = $cacheState;
        $this->_dataHelper      = $dataHelper;
        $this->_objectManager   = $objectManager;
        $this->_application     = $application;
        $this->_themeContext    = $themeContext;
    }

    /**
     * Update system data for current VDE environment
     *
     * @param string $areaCode
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function update($areaCode, Magento_Core_Controller_Request_Http $request)
    {
        $mode = $request->getAlias('editorMode') ?: self::MODE_NAVIGATION;
        $this->_themeContext->setEditableThemeById($request->getAlias('themeId'));

        if (!$request->isAjax()) {
            $this->_backendSession->setData(self::CURRENT_URL_SESSION_KEY, $request->getPathInfo());
            $this->_backendSession->setData(self::CURRENT_MODE_SESSION_KEY, $mode);
        }
        $this->_injectUrlModel($mode);
        $this->_injectLayout($mode, $areaCode);
        $this->_setTheme();
        $this->_disableCache();
    }

    /**
     * Reset VDE state data
     *
     * @return Magento_DesignEditor_Model_State
     */
    public function reset()
    {
        $this->_backendSession->unsetData(self::CURRENT_URL_SESSION_KEY)
            ->unsetData(self::CURRENT_MODE_SESSION_KEY);
        $this->_themeContext->reset();
        return $this;
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
            case self::MODE_NAVIGATION:
            default:
                $this->_layoutFactory->createLayout(array('area' => $areaCode), self::LAYOUT_NAVIGATION_CLASS_NAME);
                break;
        }
    }

    /**
     * Create url model instance that will be used instead of Magento_Core_Model_Url in navigation mode
     */
    protected function _injectUrlModel($mode)
    {
        switch ($mode) {
            case self::MODE_NAVIGATION:
            default:
                $this->_urlModelFactory->replaceClassName(self::URL_MODEL_NAVIGATION_MODE_CLASS_NAME);
                break;
        }
    }

    /**
     * Set current VDE theme
     */
    protected function _setTheme()
    {
        if ($this->_themeContext->getEditableTheme()) {
            $themeId = $this->_themeContext->getVisibleTheme()->getId();
            $this->_application->getStore()->setConfig(
                Magento_Core_Model_View_Design::XML_PATH_THEME_ID,
                $themeId
            );
            $this->_application->getConfig()->setNode(
                'default/' . Magento_Core_Model_View_Design::XML_PATH_THEME_ID,
                $themeId
            );
        }
    }

    /**
     * Disable some cache types in VDE mode
     */
    protected function _disableCache()
    {
        foreach ($this->_dataHelper->getDisabledCacheTypes() as $cacheCode) {
            if ($this->_cacheState->isEnabled($cacheCode)) {
                $this->_cacheState->setEnabled($cacheCode, false);
            }
        }
    }
}
