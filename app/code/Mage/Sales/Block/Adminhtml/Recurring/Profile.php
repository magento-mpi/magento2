<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders block
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Instructions to create child grid
     *
     * @var string
     */
    protected $_blockGroup = 'Mage_Sales';
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
