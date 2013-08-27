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
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param array $data
     */
    public function __construct(Magento_Core_Model_View_Url $viewUrl, array $data = array())
    {
        $this->_viewUrl = $viewUrl;
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
            'widget_placeholders' => Mage::getModel('Magento_Widget_Model_Widget')->getPlaceholderImageUrls(),
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
            $all = Mage::getModel('Magento_Widget_Model_Widget')->getWidgetsXml();
            $filtered = Mage::getModel('Magento_Widget_Model_Widget')->getWidgetsXml($config->getData('widget_filters'));
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
        return Mage::getSingleton('Magento_Backend_Model_Url')->getUrl('*/widget/index', $params);
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
        return Mage::helper('Magento_Core_Helper_Data')->urlEncode($param);
    }

    /**
     * Decode URL query param and return list of widgets
     *
     * @param string $queryParam Query param value to decode
     * @return array Array of widget types
     */
    public function decodeWidgetsFromQuery($queryParam)
    {
        $param = Mage::helper('Magento_Core_Helper_Data')->urlDecode($queryParam);
        return preg_split('/\s*\,\s*/', $param, 0, PREG_SPLIT_NO_EMPTY);
    }

}
