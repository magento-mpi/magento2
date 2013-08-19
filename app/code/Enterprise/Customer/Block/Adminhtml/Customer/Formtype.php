<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Form Types Grid Container Block
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Formtype extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Enterprise_Customer';
        $this->_controller = 'adminhtml_customer_formtype';
        $this->_headerText = __('Manage Form Types');

        parent::_construct();

        $this->_updateButton('add', 'label', __('New Form Type'));
    }
}
