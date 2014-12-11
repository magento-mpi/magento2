<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
