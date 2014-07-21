<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Index;

class ChangeLocale extends \Magento\Backend\Controller\Adminhtml\Index
{
    /**
     * Change locale action
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
    }
}
