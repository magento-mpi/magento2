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

namespace Magento\Widget\Model\Resource\Widget\Instance\Options;

class Types implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Widget\Model\Widget\Instance
     */
    protected $_model;

    /**
     * @param \Magento\Widget\Model\Widget\Instance $widgetInstanceModel
     */
    public function __construct(\Magento\Widget\Model\Widget\Instance $widgetInstanceModel)
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
