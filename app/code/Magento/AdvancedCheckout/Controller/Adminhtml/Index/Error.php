<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

class Error extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * Empty page for final errors occurred
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_initTitle();
        $this->_view->renderLayout();
    }
}
