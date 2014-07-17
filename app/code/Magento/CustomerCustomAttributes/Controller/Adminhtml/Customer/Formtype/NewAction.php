<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype;

class NewAction extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype
{
    /**
     * Create new form type by skeleton
     *
     * @return void
     */
    public function execute()
    {
        $this->_coreRegistry->register('edit_mode', 'new');
        $this->_initFormType();
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
