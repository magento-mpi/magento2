<?php
/**
 * Web API role edit page.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method \Magento\Webapi\Block\Adminhtml\Role\Edit setApiRole() setApiRole(\Magento\Webapi\Model\Acl\Role $role)
 * @method \Magento\Webapi\Model\Acl\Role getApiRole() getApiRole()
 */
namespace Magento\Webapi\Block\Adminhtml\Role;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
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
