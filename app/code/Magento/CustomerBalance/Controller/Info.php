<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customerbalance controller for My Account
 *
 */
namespace Magento\CustomerBalance\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Info extends \Magento\App\Action\Action
{
    /**
     * Authenticate customer
     *
     * @param RequestInterface $request
     * @return $this|mixed
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Store Credit dashboard
     */
    public function indexAction()
    {
        if (!$this->_objectManager->get('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
            $this->_redirect('customer/account/');
            return;
        }
        $this->loadLayout();
        $this->_layoutServices->getLayout()->initMessages('Magento\Customer\Model\Session');
        $this->loadLayoutUpdates();
        $headBlock = $this->_layoutServices->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Store Credit'));
        }
        $this->renderLayout();
    }
}
