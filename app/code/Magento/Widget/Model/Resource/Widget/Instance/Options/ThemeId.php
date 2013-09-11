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
 * Widget Instance Theme Id Options
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Widget\Model\Resource\Widget\Instance\Options;

class ThemeId implements \Magento\Core\Model\Option\ArrayInterface
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

    public function toOptionArray()
    {
        return $this->_resourceModel->toOptionHash();
    }
}
