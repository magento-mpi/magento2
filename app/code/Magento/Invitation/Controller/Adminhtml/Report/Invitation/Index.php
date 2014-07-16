<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Adminhtml\Report\Invitation;

class Index extends \Magento\Invitation\Controller\Adminhtml\Report\Invitation
{
    /**
     * General report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Invitations Report'));

        $this->_initAction()->_setActiveMenu(
            'Magento_Invitation::report_magento_invitation_general'
        )->_addBreadcrumb(
            __('General Report'),
            __('General Report')
        );
        $this->_view->renderLayout();
    }
}
