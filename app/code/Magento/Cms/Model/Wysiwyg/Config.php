<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Model\Wysiwyg;

/**
 * Wysiwyg Config for Editor HTML Element
 */
class Config extends \Magento\Object
{
    /**
     * Wysiwyg behaviour
     */
    const WYSIWYG_ENABLED = 'enabled';
    const WYSIWYG_HIDDEN = 'hidden';
    const WYSIWYG_DISABLED = 'disabled';

    /**
     * Wysiwyg image directory
     */
    const IMAGE_DIRECTORY = 'wysiwyg';

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\View\Url
     */
    protected $_viewUrl;

    /**
     * @var \Magento\Core\Model\Variable\Config
     */
    protected $_variableConfig;

    /**
     * @var \Magento\Widget\Model\Widget\Config
     */
    protected $_widgetConfig;

    /**
     * Cms data
     *
     * @var \Magento\Cms\Helper\Data
     */
    protected $_cmsData;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var array
     */
    protected $_windowSize;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Cms\Helper\Data $cmsData
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\View\Url $viewUrl
     * @param \Magento\Core\Model\Variable\Config $variableConfig
     * @param \Magento\Widget\Model\Widget\Config $widgetConfig
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param array $windowSize
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Cms\Helper\Data $cmsData,
        \Magento\AuthorizationInterface $authorization,
        \Magento\View\Url $viewUrl,
        \Magento\Core\Model\Variable\Config $variableConfig,
        \Magento\Widget\Model\Widget\Config $widgetConfig,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        array $windowSize = array(),
        array $data = array()
    ) {
        $this->_backendUrl = $backendUrl;
        $this->_eventManager = $eventManager;
        $this->_cmsData = $cmsData;
        $this->_storeConfig = $coreStoreConfig;
        $this->_authorization = $authorization;
        $this->_viewUrl = $viewUrl;
        $this->_variableConfig = $variableConfig;
        $this->_widgetConfig = $widgetConfig;
        $this->_windowSize = $windowSize;
        parent::__construct($data);
    }

    /**
     * Return Wysiwyg config as \Magento\Object
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
     * @param array|\Magento\Object $data \Magento\Object constructor params to override default config values
     * @return \Magento\Object
     */
    public function getConfig($data = array())
    {
        $config = new \Magento\Object();

        $config->setData(array(
            'enabled'                       => $this->isEnabled(),
            'hidden'                        => $this->isHidden(),
            'use_container'                 => false,
            'add_variables'                 => true,
            'add_widgets'                   => true,
            'no_display'                    => false,
            'translator'                    => $this->_cmsData,
            'encode_directives'             => true,
            'directives_url'                => $this->_backendUrl->getUrl('cms/wysiwyg/directive'),
            'popup_css'                     => $this->_viewUrl->getViewFileUrl(
                'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/dialog.css'
            ),
            'content_css'                   => $this->_viewUrl->getViewFileUrl(
                'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/content.css'
            ),
            'width'                         => '100%',
            'plugins'                       => array()
        ));

        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));

        if ($this->_authorization->isAllowed('Magento_Cms::media_gallery')) {
            $config->addData(array(
                'add_images' => true,
                'files_browser_window_url' => $this->_backendUrl->getUrl('cms/wysiwyg_images/index'),
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
        $wysiwygState = $this->_storeConfig->getValue('cms/wysiwyg/enabled', \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $this->getStoreId());
        return in_array($wysiwygState, array(self::WYSIWYG_ENABLED, self::WYSIWYG_HIDDEN));
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->_storeConfig->getValue('cms/wysiwyg/enabled', \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE) == self::WYSIWYG_HIDDEN;
    }
}
