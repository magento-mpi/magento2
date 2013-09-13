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
    /** @var  Magento_Widget_Model_Widget */
    protected $_widget;

    /** @var  Magento_Widget_Model_Resource_Widget */
    protected $_widgetResource;

    /** @var  Magento_Core_Model_App */
    protected $_coreApp;

    /**
     * Constructor
     *
     * @param Magento_Widget_Model_Widget $widget
     * @param Magento_Widget_Model_Resource_Widget $widgetResource
     * @param Magento_Core_Model_App $coreApp
     * @param Magento_Core_Model_View_Url $viewUrl
     */
    public function __construct(
        Magento_Widget_Model_Widget $widget,
        Magento_Widget_Model_Resource_Widget $widgetResource,
        Magento_Core_Model_App $coreApp,
        Magento_Core_Model_View_Url $viewUrl
    ) {
        $this->_widget = $widget;
        $this->_widgetResource = $widgetResource;
        $this->_coreApp = $coreApp;
        parent::__construct($viewUrl);
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
            $preconfigured = $this->_widgetResource->loadPreconfiguredWidget($params['id']);
            $type = $preconfigured['widget_type'];
            $params = $preconfigured['parameters'];
        } else {
            return '';
        }
        
        // we have no other way to avoid fatal errors for type like 'cms/widget__link', '_cms/widget_link' etc. 
        $xml = $this->_widget->getWidgetByClassType($type);
        if ($xml === null) {
            return '';
        }
        
        /**
         * define widget block and check the type is instance of Widget Interface
         * @var Magento_Core_Block_Abstract $widget
         */
        $widget = $this->_coreApp->getLayout()->createBlock($type, $name, array('data' => $params));
        if (!$widget instanceof Magento_Widget_Block_Interface) {
            return '';
        }

        return $widget->toHtml();
    }
}
