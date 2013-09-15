<?php
/**
 * Web API Role edit form.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Class Magento_Webapi_Block_Adminhtml_Role_Edit_Form
 *
namespace Magento\Webapi\Block\Adminhtml\Role\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form container.
     *
     * @return \Magento\Webapi\Block\Adminhtml\Role\Edit\Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'action' => $this->getUrl('*/*/save'),
                'id' => 'edit_form',
                'method' => 'post',
            ))
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
