<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Creditmemo;

class Index extends \Magento\Sales\Controller\Adminhtml\Creditmemo\AbstractCreditmemo\Index
{
    /**
     * Index page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Credit Memos'));
        parent::execute();
    }
}
