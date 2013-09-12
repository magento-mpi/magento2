<?php
/**
 * Web API role edit page.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method Magento_Webapi_Block_Adminhtml_Role_Edit setApiRole() setApiRole(Magento_Webapi_Model_Acl_Role $role)
 * @method Magento_Webapi_Model_Acl_Role getApiRole() getApiRole()
 */
class Magento_Webapi_Block_Adminhtml_Role_Edit extends Magento_Backend_Block_Widget_Form_Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Webapi';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_role';

    /**
     * @var string
     */
    protected $_objectId = 'role_id';

    /**
     * Internal Constructor.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_formScripts[] = "function saveAndContinueEdit(url)" .
            "{var tagForm = new varienForm('edit_form'); tagForm.submit(url);}";

        $this->_addButton('save_and_continue', array(
            'label' => __('Save and Continue Edit'),
            'onclick' => "saveAndContinueEdit('" . $this->getSaveAndContinueUrl() . "')",
            'class' => 'save'
        ), 100);

        $this->_updateButton('save', 'label', __('Save API Role'));
        $this->_updateButton('delete', 'label', __('Delete API Role'));
    }

    /**
     * Retrieve role SaveAndContinue URL.
     *
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'continue' => true));
    }

    /**
     * Get header text.
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getApiRole()->getId()) {
            return __("Edit API Role '%1'", $this->escapeHtml($this->getApiRole()->getRoleName()));
        } else {
            return __('New API Role');
        }
    }
}
