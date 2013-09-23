<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wysiwyg Config for Editor HTML Element
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Model_Wysiwyg_Config extends Magento_Object
{
    /**
     * Wysiwyg behaviour
     */
    const WYSIWYG_ENABLED = 'enabled';
    const WYSIWYG_HIDDEN = 'hidden';
    const WYSIWYG_DISABLED = 'disabled';
    const IMAGE_DIRECTORY = 'wysiwyg';

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @var Magento_Core_Model_Variable_Config
     */
    protected $_variableConfig;

    /**
     * @var Magento_Widget_Model_Widget_Config
     */
    protected $_widgetConfig;

    /**
     * Cms data
     *
     * @var Magento_Cms_Helper_Data
     */
    protected $_cmsData = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var array
     */
    protected $_windowSize;
    
    /**
     * @var Magento_Backend_Model_Url
     */
    protected $_backendUrl;

    /**
     * @param Magento_Backend_Model_Url $backendUrl
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Cms_Helper_Data $cmsData
     * @param Magento_AuthorizationInterface $authorization
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Core_Model_Variable_Config $variableConfig
     * @param Magento_Widget_Model_Widget_Config $widgetConfig
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $windowSize
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Model_Url $backendUrl,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Cms_Helper_Data $cmsData,
        Magento_AuthorizationInterface $authorization,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Core_Model_Variable_Config $variableConfig,
        Magento_Widget_Model_Widget_Config $widgetConfig,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $windowSize = array(),
        array $data = array()
    ) {
        $this->_backendUrl = $backendUrl;
        $this->_eventManager = $eventManager;
        $this->_cmsData = $cmsData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_authorization = $authorization;
        $this->_viewUrl = $viewUrl;
        $this->_variableConfig = $variableConfig;
        $this->_widgetConfig = $widgetConfig;
        $this->_windowSize = $windowSize;
        parent::__construct($data);
    }

    /**
     * Return Wysiwyg config as Magento_Object
     *
     * Config options description:
     *
     * enabled:                 Enabled Visual Editor or not
     * hidden:                  Show Visual Editor on page load or not
     * use_container:           Wrap Editor contents into div or not
     * no_display:              Hide Editor container or not (related to use_container)
     * translator:              Helper to translate phrases in lib
     * files_browser_*:         Files Browser (media, images) settings
     * encode_directives:       Encode template directives with JS or not
     *
     * @param array|Magento_Object $data Magento_Object constructor params to override default config values
     * @return Magento_Object
     */
    public function getConfig($data = array())
    {
        $config = new Magento_Object();
        $viewUrl = $this->_viewUrl;

        $config->setData(array(
            'enabled'                       => $this->isEnabled(),
            'hidden'                        => $this->isHidden(),
            'use_container'                 => false,
            'add_variables'                 => true,
            'add_widgets'                   => true,
            'no_display'                    => false,
            'translator'                    => $this->_cmsData,
            'encode_directives'             => true,
            'directives_url'                => $this->_backendUrl->getUrl('*/cms_wysiwyg/directive'),
            'popup_css'                     =>
                $viewUrl->getViewFileUrl('mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/dialog.css'),
            'content_css'                   =>
                $viewUrl->getViewFileUrl('mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/content.css'),
            'width'                         => '100%',
            'plugins'                       => array()
        ));

        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));

        if ($this->_authorization->isAllowed('Magento_Cms::media_gallery')) {
            $config->addData(array(
                'add_images' => true,
                'files_browser_window_url' => $this->_backendUrl->getUrl('*/cms_wysiwyg_images/index'),
                'files_browser_window_width' => $this->_windowSize['width'],
                'files_browser_window_height'=> $this->_windowSize['height'],
            ));
        }

        if (is_array($data)) {
            $config->addData($data);
        }

        if ($config->getData('add_variables')) {
            $settings = $this->_variableConfig->getWysiwygPluginSettings($config);
            $config->addData($settings);
        }

        if ($config->getData('add_widgets')) {
            $settings = $this->_widgetConfig->getPluginSettings($config);
            $config->addData($settings);
        }

        return $config;
    }

    /**
     * Return URL for skin images placeholder
     *
     * @return string
     */
    public function getSkinImagePlaceholderUrl()
    {
        return $this->_viewUrl->getViewFileUrl('Magento_Cms::images/wysiwyg_skin_image.png');
    }

    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        $wysiwygState = $this->_coreStoreConfig->getConfig('cms/wysiwyg/enabled', $this->getStoreId());
        return in_array($wysiwygState, array(self::WYSIWYG_ENABLED, self::WYSIWYG_HIDDEN));
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->_coreStoreConfig->getConfig('cms/wysiwyg/enabled') == self::WYSIWYG_HIDDEN;
    }
}
