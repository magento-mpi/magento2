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
 * User block
 *
 * @category   Mage
 * @package    Mage_User
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Block_User extends Mage_Backend_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'user';
        $this->_blockGroup = 'Mage_User';
        $this->_headerText = Mage::helper('Mage_User_Helper_Data')->__('Users');
        $this->_addButtonLabel = Mage::helper('Mage_User_Helper_Data')->__('Add New User');
        parent::__construct();
    }

    /**
     * Prepare output HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('permissions_user_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
