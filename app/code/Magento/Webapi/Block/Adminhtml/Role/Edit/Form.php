<?php
/**
 * Web API Role edit form.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Block\Adminhtml\Role\Edit;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Prepare form container.
     *
     * @return \Magento\Webapi\Block\Adminhtml\Role\Edit\Form
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form(array(
            'action' => $this->getUrl('*/*/save'),
            'id' => 'edit_form',
            'method' => 'post'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
