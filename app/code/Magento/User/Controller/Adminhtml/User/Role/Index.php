<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Controller\Adminhtml\User\Role;

class Index extends \Magento\User\Controller\Adminhtml\User\Role
{
    /**
     * Show grid with roles existing in systems
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Roles'));

        $this->_initAction();

        $this->_view->renderLayout();
    }
}
