<?php
/**
 * Web API user edit form.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method \Magento\Webapi\Block\Adminhtml\User\Edit setApiUser() setApiUser(\Magento\Webapi\Model\Acl\User $user)
 * @method \Magento\Webapi\Model\Acl\User getApiUser() getApiUser()
 */
namespace Magento\Webapi\Block\Adminhtml\User\Edit;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Prepare Form.
     *
     * @return \Magento\Webapi\Block\Adminhtml\User\Edit\Form
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form();
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
