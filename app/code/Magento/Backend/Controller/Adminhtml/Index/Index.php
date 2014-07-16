<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\Controller\Adminhtml\Index
{
    /**
     * Admin area entry point
     * Always redirects to the startup page url
     *
     * @return void
     */
    public function execute()
    {
        $this->_redirect($this->_backendUrl->getStartupPageUrl());
    }
}
