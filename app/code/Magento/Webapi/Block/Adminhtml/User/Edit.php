<?php
/**
 * Web API user edit page.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method \Magento\Object getApiUser() getApiUser()
 * @method \Magento\Webapi\Block\Adminhtml\User\Edit setApiUser() setApiUser(\Magento\Object $apiUser)
 */
namespace Magento\Webapi\Block\Adminhtml\User;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Webapi';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_user';

    /**
     * @var string
     */
    protected $_objectId = 'user_id';

    /**
     * Internal constructor.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_addButton('save_and_continue', array(
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 100);

        $this->_updateButton('save', 'label', __('Save API User'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', __('Delete API User'));
    }

    /**
     * Set Web API user to child form block.
     *
     * @return \Magento\Webapi\Block\Adminhtml\User\Edit
     */
    protected function _beforeToHtml()
    {
        /** @var $formBlock \Magento\Webapi\Block\Adminhtml\User\Edit\Form */
        $formBlock = $this->getChildBlock('form');
        $formBlock->setApiUser($this->getApiUser());
        return parent::_beforeToHtml();
    }

    /**
     * Get header text.
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getApiUser()->getId()) {
            return __("Edit API User '%1'", $this->escapeHtml($this->getApiUser()->getApiKey()));
        } else {
            return __('New API User');
        }
    }
}
