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
namespace Magento\Cms\Model\Wysiwyg;

class Config extends \Magento\Object
{
    /**
     * Wysiwyg behaviour
     */
    const WYSIWYG_ENABLED = 'enabled';
    const WYSIWYG_HIDDEN = 'hidden';
    const WYSIWYG_DISABLED = 'disabled';
    const IMAGE_DIRECTORY = 'wysiwyg';

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Core\Model\View\Url
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
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Cms_Helper_Data $cmsData
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param Magento_Core_Model_Variable_Config $variableConfig
     * @param Magento_Widget_Model_Widget_Config $widgetConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Cms_Helper_Data $cmsData,
        \Magento\AuthorizationInterface $authorization,
        \Magento\Core\Model\View\Url $viewUrl,
        Magento_Core_Model_Variable_Config $variableConfig,
        Magento_Widget_Model_Widget_Config $widgetConfig,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_cmsData = $cmsData;
        $this->_authorization = $authorization;
        $this->_viewUrl = $viewUrl;
        $this->_variableConfig = $variableConfig;
        $this->_widgetConfig = $widgetConfig;
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
            'directives_url'                =>
                \Mage::getSingleton('Magento\Backend\Model\Url')->getUrl('*/cms_wysiwyg/directive'),
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
                'files_browser_window_url' => \Mage::getSingleton('Magento\Backend\Model\Url')
                    ->getUrl('*/cms_wysiwyg_images/index'),
                'files_browser_window_width' => (int) \Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
                'files_browser_window_height'=> (int) \Mage::getConfig()->getNode('adminhtml/cms/browser/window_height'),
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
        $wysiwygState = \Mage::getStoreConfig('cms/wysiwyg/enabled', $this->getStoreId());
        return in_array($wysiwygState, array(self::WYSIWYG_ENABLED, self::WYSIWYG_HIDDEN));
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return \Mage::getStoreConfig('cms/wysiwyg/enabled') == self::WYSIWYG_HIDDEN;
    }
}
