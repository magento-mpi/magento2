<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Controller\Adminhtml\Customerbalance;

class GridHistory extends \Magento\CustomerBalance\Controller\Adminhtml\Customerbalance
{
    /**
     * Customer balance grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Balance\History\Grid'
            )->toHtml()
        );
    }
}
