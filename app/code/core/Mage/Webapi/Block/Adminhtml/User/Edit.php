<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API User edit page
 *
 * @method Mage_Webapi_Block_Adminhtml_User_Edit setApiUser(Mage_Webapi_Model_Acl_User $user)
 * @method Mage_Webapi_Model_Acl_User getApiUser()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_User_Edit extends Mage_Backend_Block_Widget_Form_Container
{
    /**
     * Initialize form container
     */
    public function __construct()
    {
        $this->_blockGroup = 'Mage_Webapi';
        $this->_objectId = 'user_id';
        $this->_controller = 'adminhtml_user';

        parent::__construct();

        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save'
        ), 100);

        $this->_formScripts[] = "function saveAndContinueEdit()" .
        "{editForm.submit($('edit_form').action + 'back/edit/')}";

        $this->_updateButton('save', 'label', Mage::helper('Mage_Webapi_Helper_Data')->__('Save API User'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Webapi_Helper_Data')->__('Delete API User'));
    }

    /**
     * Set Web API User to child form block
     *
     * @return Mage_Webapi_Block_Adminhtml_User_Edit
     */
    protected function _beforeToHtml()
    {
        /** @var $formBlock Mage_Webapi_Block_Adminhtml_User_Edit_Form */
        $formBlock = $this->getChildBlock('form');
        $formBlock->setApiUser($this->getApiUser());
        return parent::_beforeToHtml();
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getApiUser()->getId()) {
            return Mage::helper('Mage_Webapi_Helper_Data')
                ->__("Edit API User '%s'", $this->escapeHtml($this->getApiUser()->getContactEmail()));
        } else {
            return Mage::helper('Mage_Webapi_Helper_Data')->__('New API User');
        }
    }
}
