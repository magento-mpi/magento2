<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders block
 */
class Magento_Sales_Block_Adminhtml_Recurring_Profile extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Instructions to create child grid
     *
     * @var string
     */
    protected $_blockGroup = 'Magento_Sales';
    protected $_controller = 'adminhtml_recurring_profile';

    /**
     * Set header text and remove "add" btn
     */
    protected function _construct()
    {
        $this->_headerText = __('Recurring Billing Profiles (beta)');
        parent::_construct();
        $this->_removeButton('add');
    }
}
