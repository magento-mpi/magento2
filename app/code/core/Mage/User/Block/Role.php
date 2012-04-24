<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mage_User role block
 *
 * @category   Mage
 * @package    Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_User_Block_Role extends Mage_Backend_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'role';
        $this->_blockGroup = 'Mage_User';
        $this->_headerText = Mage::helper('Mage_User_Helper_Data')->__('Roles');
        $this->_addButtonLabel = Mage::helper('Mage_User_Helper_Data')->__('Add New Role');
        parent::__construct();
    }
}
