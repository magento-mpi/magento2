<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Template Filter Model
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Model_Template_Filter extends Mage_Cms_Model_Template_Filter
{
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
            $preconfigured = Mage::getResourceSingleton('Mage_Widget_Model_Resource_Widget')
                ->loadPreconfiguredWidget($params['id']);
            $type = $preconfigured['widget_type'];
            $params = $preconfigured['parameters'];
        } else {
            return '';
        }
        
        // we have no other way to avoid fatal errors for type like 'cms/widget__link', '_cms/widget_link' etc. 
        $xml = Mage::getSingleton('Mage_Widget_Model_Widget')->getWidgetByClassType($type);
        if ($xml === null) {
            return '';
        }
        
        // define widget block and check the type is instance of Widget Interface
        $widget = Mage::app()->getLayout()->createBlock($type, $name, array('data' => $params));
        if (!$widget instanceof Mage_Widget_Block_Interface) {
            return '';
        }

        return $widget->toHtml();
    }
}
