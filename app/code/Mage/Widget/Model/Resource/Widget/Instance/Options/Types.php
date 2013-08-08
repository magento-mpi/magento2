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
 * Widget Instance Types Options
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Widget_Model_Resource_Widget_Instance_Options_Types implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Widget_Model_Widget_Instance
     */
    protected $_model;

    /**
     * @param Mage_Widget_Model_Widget_Instance $widgetInstanceModel
     */
    public function __construct(Mage_Widget_Model_Widget_Instance $widgetInstanceModel)
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
