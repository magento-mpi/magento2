<?php
/**
 * Widgets Insertion Plugin Config for Editor HTML Element
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Model\Widget;

class Config
{
    /**
     * @var \Magento\View\Url
     */
    protected $_viewUrl;

    /**
     * @var \Magento\Widget\Model\Widget
     */
    protected $_widget;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @var \Magento\Widget\Model\WidgetFactory
     */
    protected $_widgetFactory;

    /**
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\View\Url $viewUrl
     * @param \Magento\Widget\Model\WidgetFactory $widgetFactory
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\View\Url $viewUrl,
        \Magento\Widget\Model\WidgetFactory $widgetFactory
    ) {
        $this->_backendUrl = $backendUrl;
        $this->_coreHelper = $coreHelper;
        $this->_viewUrl = $viewUrl;
        $this->_widgetFactory = $widgetFactory;
    }

    /**
     * Return config settings for widgets insertion plugin based on editor element config
     *
     * @param \Magento\Object $config
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
     * @param \Magento\Object $config Editor element config
     * @return string
     */
    public function getWidgetWindowUrl($config)
    {
        $params = array();

        $skipped = is_array($config->getData('skip_widgets')) ? $config->getData('skip_widgets') : array();
        if ($config->hasData('widget_filters')) {
            $all = $this->_widgetFactory->create()->getWidgets();
            $filtered = $this->_widgetFactory->create()->getWidgets($config->getData('widget_filters'));
            foreach ($all as $code => $widget) {
                if (!isset($filtered[$code])) {
                    $skipped[] = $widget['@']['type'];
                }
            }
        }

        if (count($skipped) > 0) {
            $params['skip_widgets'] = $this->encodeWidgetsToQuery($skipped);
        }
        return $this->_backendUrl->getUrl('adminhtml/widget/index', $params);
    }

    /**
     * Encode list of widget types into query param
     *
     * @param string[]|string $widgets List of widgets
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
     * @return string[] Array of widget types
     */
    public function decodeWidgetsFromQuery($queryParam)
    {
        $param = $this->_coreHelper->urlDecode($queryParam);
        return preg_split('/\s*\,\s*/', $param, 0, PREG_SPLIT_NO_EMPTY);
    }

}
