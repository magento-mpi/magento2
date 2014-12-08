<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Create;


class ProcessData extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * Process data and display index page
     *
     * @return void
     */
    public function execute()
    {
        $this->_initSession();
        $this->_processData();
        $this->_forward('index');
    }
}
