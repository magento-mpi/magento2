<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerBalance\Controller\Info;

use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Authenticate customer
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Store Credit dashboard
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_objectManager->get('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
            $this->_redirect('customer/account/');
            return;
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->loadLayoutUpdates();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Store Credit'));
        $this->_view->renderLayout();
    }
}
