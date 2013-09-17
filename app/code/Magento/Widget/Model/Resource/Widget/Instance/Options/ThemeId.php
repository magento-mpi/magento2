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

class Magento_Widget_Model_Resource_Widget_Instance_Options_ThemeId implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Widget_Model_Widget_Instance
     */
    protected $_resourceModel;

    /**
     * @param Magento_Core_Model_Resource_Theme_Collection $widgetResourceModel
     */
    public function __construct(Magento_Core_Model_Resource_Theme_Collection $widgetResourceModel)
    {
        $this->_resourceModel = $widgetResourceModel;
    }

    public function toOptionArray()
    {
        return $this->_resourceModel->toOptionHash();
    }
}
