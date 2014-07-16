<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class Index extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Segments list
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Customer Segments'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_CustomerSegment::customer_customersegment');
        $this->_view->renderLayout();
    }
}
