<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Form Types Grid Container Block
 *
 * @category   Magento
 * @package    Magento_CustomerCustomAttributes
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_CustomerCustomAttributes';
        $this->_controller = 'adminhtml_customer_formtype';
        $this->_headerText = __('Manage Form Types');

        parent::_construct();

        $this->_updateButton('add', 'label', __('New Form Type'));
    }
}
