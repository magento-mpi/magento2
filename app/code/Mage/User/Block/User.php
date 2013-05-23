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
    protected function _construct()
    {
        $this->addData(array(
            Mage_Backend_Block_Widget_Container::PARAM_CONTROLLER => 'user',
            Mage_Backend_Block_Widget_Grid_Container::PARAM_BLOCK_GROUP => 'Mage_User',
            Mage_Backend_Block_Widget_Grid_Container::PARAM_BUTTON_NEW => $this->__('Add New User'),
            Mage_Backend_Block_Widget_Container::PARAM_HEADER_TEXT => $this->__('Users'),
        ));
        parent::_construct();
    }

    /**
     * Prepare output HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('permissions_user_html_before', array('block' => $this));
        $this->_addNewButton();
        /** @var $model Mage_User_Model_Resource_User */
        $model = Mage::getObjectManager()->get('Mage_User_Model_Resource_User');
        if (!$model->canCreateUser()) {
            $this->_updateButton('add', 'disabled', true);
        }
        return parent::_toHtml();
    }
}
