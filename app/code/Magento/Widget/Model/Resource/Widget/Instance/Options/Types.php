<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Widget Instance Types Options
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Widget\Model\Resource\Widget\Instance\Options;

class Types implements \Magento\Framework\Option\ArrayInterface
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

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $widgets = array();
        $widgetsOptionsArr = $this->_model->getWidgetsOptionArray('type');
        foreach ($widgetsOptionsArr as $widget) {
            $widgets[$widget['value']] = $widget['label'];
        }
        return $widgets;
    }
}
