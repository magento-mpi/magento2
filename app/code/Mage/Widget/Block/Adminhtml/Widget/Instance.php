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
 * Widget Instance grid container
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mage_Widget';
        $this->_controller = 'adminhtml_widget_instance';
        $this->_headerText = Mage::helper('Mage_Widget_Helper_Data')->__('Manage Widget Instances');
        parent::_construct();
        $this->_updateButton('add', 'label', Mage::helper('Mage_Widget_Helper_Data')->__('Add New Widget Instance'));
    }
}
