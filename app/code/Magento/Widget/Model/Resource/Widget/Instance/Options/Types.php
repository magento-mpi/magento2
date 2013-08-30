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
 * Widget Instance Types Options
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Widget_Model_Resource_Widget_Instance_Options_Types implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Widget_Model_Widget_Instance
     */
    protected $_model;

    /**
     * @param Magento_Widget_Model_Widget_Instance $widgetInstanceModel
     */
    public function __construct(Magento_Widget_Model_Widget_Instance $widgetInstanceModel)
    {
        $this->_model = $widgetInstanceModel;
    }

    public function toOptionArray()
    {
        $widgets = array();
        $widgetsOptionsArr = $this->_model->getWidgetsOptionArray();
        foreach ($widgetsOptionsArr as $widget) {
            $widgets[$widget['value']] = $widget['label'];
        }
        return $widgets;
    }
}
