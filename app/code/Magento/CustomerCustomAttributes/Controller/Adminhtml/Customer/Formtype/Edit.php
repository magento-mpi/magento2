<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype;

class Edit extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype
{
    /**
     * Edit Form Type
     *
     * @return void
     */
    public function execute()
    {
        $this->_coreRegistry->register('edit_mode', 'edit');
        $this->_initFormType();
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
