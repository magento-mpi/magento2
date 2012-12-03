<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Adminhtml_Launcher_Preview extends Mage_Core_Block_Template
{
    /**#@+
     * Preview modes
     */
    const TYPE_DEFAULT = 'default';
    const TYPE_DEMO    = 'demo';
    /**#@-*/

    /**
     * Theme parameter name
     */
    const PARAM_THEME_ID = 'theme_id';

    /**
     * Theme factory
     *
     * @var Mage_Core_Model_Theme_Factory
     */
    protected $_themeFactory;

    /**
     * Current theme used for preview
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Design session
     *
     * @var Mage_DesignEditor_Model_Session
     */
    protected $_designSession;

    /**
     * Initialize dependencies
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     * @param Mage_DesignEditor_Model_Session $designSession
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Theme_Factory $themeFactory,
        Mage_DesignEditor_Model_Session $designSession,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data
        );
        $this->_themeFactory = $themeFactory;
        $this->_designSession = $designSession;
    }

    /**
     * Get current theme for preview
     *
     * @return Mage_Core_Model_Theme
     * @throws Magento_Exception
     */
    public function getTheme()
    {
        if ($this->_theme) {
            return $this->_theme;
        }

        $themeId = (int)$this->getRequest()->getParam(self::PARAM_THEME_ID);
        if (!$themeId) {
            throw new Magento_Exception($this->__('You need to set theme for preview'));
        }
        $this->_theme = $this->_themeFactory->create()->load($themeId);
        return $this->_theme;
    }

    /**
     * Return preview store url
     *
     * @param string $type
     * @return string
     * @throws Magento_Exception
     */
    public function getPreviewUrl($type = self::TYPE_DEFAULT)
    {
        switch ($type) {
            case self::TYPE_DEFAULT:
                $url = $this->_designSession->setThemeId($this->getTheme()->getId())->getPreviewUrl();
                break;
            case self::TYPE_DEMO:
                $url = '';
                break;
            default:
                throw new Magento_Exception($this->__('Undefined Preview Type'));
                break;
        }

        return $url;
    }
}
