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
 * Template Filter Model
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Widget_Model_Template_Filter extends Magento_Cms_Model_Template_Filter
{
    /**
     * @var Magento_Widget_Model_Resource_Widget
     */
    protected $_widgetResource;

    /**
     * @var Magento_Catalog_Model_Layer
     */
    protected $_layout;

    /**
     * @var Magento_Widget_Model_Widget
     */
    protected $_widget;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Widget_Model_Resource_Widget $widgetResource
     * @param Magento_Widget_Model_Widget $widget
     * @param Magento_Catalog_Model_Layer $layout
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Widget_Model_Resource_Widget $widgetResource,
        Magento_Widget_Model_Widget $widget,
        Magento_Catalog_Model_Layer $layout
    ) {
        $this->_widgetResource = $widgetResource;
        $this->_widget = $widget;
        $this->_layout = $layout;
        parent::__construct($coreData, $viewUrl);
    }

    /**
     * Generate widget
     *
     * @param array $construction
     * @return string
     */
    public function widgetDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);

        // Determine what name block should have in layout
        $name = null;
        if (isset($params['name'])) {
            $name = $params['name'];
        }

        // validate required parameter type or id
        if (!empty($params['type'])) {
            $type = $params['type'];
        } elseif (!empty($params['id'])) {
            $preConfigured = $this->_widgetResource->loadPreconfiguredWidget($params['id']);
            $type = $preConfigured['widget_type'];
            $params = $preConfigured['parameters'];
        } else {
            return '';
        }
        
        // we have no other way to avoid fatal errors for type like 'cms/widget__link', '_cms/widget_link' etc. 
        $xml = $this->_widget->getXmlElementByType($type);
        if ($xml === null) {
            return '';
        }
        
        // define widget block and check the type is instance of Widget Interface
        $widget = $this->_layout->createBlock($type, $name, array('data' => $params));
        if (!$widget instanceof Magento_Widget_Block_Interface) {
            return '';
        }

        return $widget->toHtml();
    }
}
