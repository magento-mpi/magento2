<?php
/**
 * Widgets Insertion Plugin Config for Editor HTML Element
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Widget_Model_Widget_Config
{
    /**
     * @var Mage_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @var Mage_Widget_Model_Widget
     */
    protected $_widget;

    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_backendUrl;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_coreHelper;

    /**
     * @param Mage_Widget_Model_Widget $widget
     * @param Mage_Backend_Model_Url $backendUrl
     * @param Mage_Core_Helper_Data $coreHelper
     * @param Mage_Core_Model_View_Url $viewUrl
     */
    public function __construct(
        Mage_Widget_Model_Widget $widget,
        Mage_Backend_Model_Url $backendUrl,
        Mage_Core_Helper_Data $coreHelper,
        Mage_Core_Model_View_Url $viewUrl
    ) {
        $this->_widget = $widget;
        $this->_backendUrl = $backendUrl;
        $this->_coreHelper = $coreHelper;
        $this->_viewUrl = $viewUrl;
    }

    /**
     * Return config settings for widgets insertion plugin based on editor element config
     *
     * @param Magento_Object $config
     * @return array
     */
    public function getPluginSettings($config)
    {
        $url = $this->_viewUrl->getViewFileUrl(
            'mage/adminhtml/wysiwyg/tiny_mce/plugins/magentowidget/editor_plugin.js'
        );
        $settings = array(
            'widget_plugin_src'   => $url,
            'widget_placeholders' => $this->_widget->getPlaceholderImageUrls(),
            'widget_window_url'   => $this->getWidgetWindowUrl($config)
        );

        return $settings;
    }

    /**
     * Return Widgets Insertion Plugin Window URL
     *
     * @param Magento_Object $config Editor element config
     * @return string
     */
    public function getWidgetWindowUrl($config)
    {
        $params = array();

        $skipped = is_array($config->getData('skip_widgets')) ? $config->getData('skip_widgets') : array();
        if ($config->hasData('widget_filters')) {
            $all = $this->_widget->getWidgets();
            $filtered = $this->_widget->getWidgets($config->getData('widget_filters'));
            foreach ($all as $code => $widget) {
                if (!isset($filtered[$code])) {
                    $skipped[] = $widget['@']['type'];
                }
            }
        }

        if (count($skipped) > 0) {
            $params['skip_widgets'] = $this->encodeWidgetsToQuery($skipped);
        }
        return $this->_backendUrl->getUrl('*/widget/index', $params);
    }

    /**
     * Encode list of widget types into query param
     *
     * @param array $widgets List of widgets
     * @return string Query param value
     */
    public function encodeWidgetsToQuery($widgets)
    {
        $widgets = is_array($widgets) ? $widgets : array($widgets);
        $param = implode(',', $widgets);
        return $this->_coreHelper->urlEncode($param);
    }

    /**
     * Decode URL query param and return list of widgets
     *
     * @param string $queryParam Query param value to decode
     * @return array Array of widget types
     */
    public function decodeWidgetsFromQuery($queryParam)
    {
        $param = $this->_coreHelper->urlDecode($queryParam);
        return preg_split('/\s*\,\s*/', $param, 0, PREG_SPLIT_NO_EMPTY);
    }

}
