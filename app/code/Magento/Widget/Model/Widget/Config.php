<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widgets Insertion Plugin Config for Editor HTML Element
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Widget_Model_Widget_Config extends Magento_Object
{
    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * @var Magento_Widget_Model_WidgetFactory
     */
    protected $_widgetFactory;

    /**
     * @var Magento_Backend_Model_Url
     */
    protected $_url;


    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Widget_Model_WidgetFactory $widgetFactory
     * @param Magento_Backend_Model_Url $url
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Widget_Model_WidgetFactory $widgetFactory,
        Magento_Backend_Model_Url $url,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_viewUrl = $viewUrl;
        $this->_widgetFactory = $widgetFactory;
        $this->_url = $url;
        parent::__construct($data);
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
            'widget_placeholders' => $this->_widgetFactory->create()->getPlaceholderImageUrls(),
            'widget_window_url'   => $this->getWidgetWindowUrl($config)
        );

        return $settings;
    }

    /**
     * Return Widgets Insertion Plugin Window URL
     *
     * @param Magento_Object Editor element config
     * @return string
     */
    public function getWidgetWindowUrl($config)
    {
        $params = array();

        $skipped = is_array($config->getData('skip_widgets')) ? $config->getData('skip_widgets') : array();
        if ($config->hasData('widget_filters')) {
            $all = $this->_widgetFactory->create()->getWidgetsXml();
            $filtered = $this->_widgetFactory->create()->getWidgetsXml($config->getData('widget_filters'));
            $reflection = new ReflectionObject($filtered);
            foreach ($all as $code => $widget) {
                if (!$reflection->hasProperty($code)) {
                    $skipped[] = $widget->getAttribute('type');
                }
            }
        }

        if (count($skipped) > 0) {
            $params['skip_widgets'] = $this->encodeWidgetsToQuery($skipped);
        }
        return $this->_url->getUrl('*/widget/index', $params);
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
        return $this->_coreData->urlEncode($param);
    }

    /**
     * Decode URL query param and return list of widgets
     *
     * @param string $queryParam Query param value to decode
     * @return array Array of widget types
     */
    public function decodeWidgetsFromQuery($queryParam)
    {
        $param = $this->_coreData->urlDecode($queryParam);
        return preg_split('/\s*\,\s*/', $param, 0, PREG_SPLIT_NO_EMPTY);
    }

}
