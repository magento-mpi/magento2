<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Creditmemo;

use Magento\Backend\App\Action;

class View extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @param Action\Context $context
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
    ) {
        $this->creditmemoLoader = $creditmemoLoader;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_creditmemo');
    }

    /**
     * Creditmemo information page
     *
     * @return void
     */
    public function execute()
    {
        $creditmemo = $this->creditmemoLoader->load($this->_request);
        if ($creditmemo) {
            if ($creditmemo->getInvoice()) {
                $this->_title->add(__("View Memo for #%1", $creditmemo->getInvoice()->getIncrementId()));
            } else {
                $this->_title->add(__("View Memo"));
            }

            $this->_view->loadLayout();
            $this->_view->getLayout()->getBlock(
                'sales_creditmemo_view'
            )->updateBackButtonUrl(
                $this->getRequest()->getParam('come_from')
            );
            $this->_setActiveMenu('Magento_Sales::sales_creditmemo');
            $this->_view->renderLayout();
        } else {
            $this->_forward('noroute');
        }
    }
}
