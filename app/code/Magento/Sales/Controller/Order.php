<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller;

use Magento\App\RequestInterface;

/**
 * Sales orders controller
 */
class Order extends \Magento\Sales\Controller\AbstractController
{
    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl();

        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this, $loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Customer order history
     *
     * @return void
     */
    public function historyAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();

        $this->_view->getLayout()->getBlock('head')->setTitle(__('My Orders'));

        $block = $this->_view->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
}
