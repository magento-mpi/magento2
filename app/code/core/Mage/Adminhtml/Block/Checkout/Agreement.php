<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin tax rule content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Checkout_Agreement extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller      = 'checkout_agreement';
        $this->_headerText      = Mage::helper('checkout')->__('Manage Terms and Conditions');
        $this->_addButtonLabel  = Mage::helper('checkout')->__('Add New Condition');
        parent::__construct();
    }
}