<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Custom Varieble Block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_System_Variable extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    protected function _construct()
    {
        $this->_controller = 'system_variable';
        $this->_headerText = __('Custom Variables');
        parent::_construct();
        $this->_updateButton('add', 'label', __('Add New Variable'));
    }
}
