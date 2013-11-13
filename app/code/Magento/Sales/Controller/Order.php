<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders controller
 */
namespace Magento\Sales\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Order extends \Magento\Sales\Controller\AbstractController
{
    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return mixed
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl();

        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Customer order history
     */
    public function historyAction()
    {
        $this->loadLayout();
        $this->_layoutServices->getLayout()->initMessages('Magento\Catalog\Model\Session');

        $this->_layoutServices->getLayout()->getBlock('head')->setTitle(__('My Orders'));

        $block = $this->_layoutServices->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->renderLayout();
    }
}
