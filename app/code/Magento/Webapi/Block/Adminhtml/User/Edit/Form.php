<?php
/**
 * Web API user edit form.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
namespace Magento\Webapi\Block\Adminhtml\User\Edit;

/**
 * @method \Magento\Webapi\Block\Adminhtml\User\Edit setApiUser() setApiUser(\Magento\Webapi\Model\Acl\User $user)
 * @method \Magento\Webapi\Model\Acl\User getApiUser() getApiUser()
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare Form.
     *
     * @return \Magento\Webapi\Block\Adminhtml\User\Edit\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
