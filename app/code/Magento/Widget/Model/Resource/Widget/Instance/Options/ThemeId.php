<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
/**
 * Widget Instance Theme Id Options
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Widget\Model\Resource\Widget\Instance\Options;

class ThemeId implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Widget\Model\Widget\Instance
     */
    protected $_resourceModel;

    /**
     * @param \Magento\Core\Model\Resource\Theme\Collection $widgetResourceModel
     */
    public function __construct(\Magento\Core\Model\Resource\Theme\Collection $widgetResourceModel)
    {
        $this->_resourceModel = $widgetResourceModel;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_resourceModel->toOptionHash();
    }
}
