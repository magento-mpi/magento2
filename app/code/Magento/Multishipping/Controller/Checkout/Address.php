<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Multishipping\Controller\Checkout;

/**
 * Multishipping checkout address manipulation controller
 */
class Address extends \Magento\Framework\App\Action\Action
{
    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->_getCheckout()->getCustomer()->getId()) {
            return $this->_redirect('customer/account/login');
        }
        return parent::dispatch($request);
    }

    /**
     * Retrieve multishipping checkout model
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    protected function _getCheckout()
    {
        return $this->_objectManager->get('Magento\Multishipping\Model\Checkout\Type\Multishipping');
    }

    /**
     * Retrieve checkout state model
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping\State
     */
    protected function _getState()
    {
        return $this->_objectManager->get('Magento\Multishipping\Model\Checkout\Type\Multishipping\State');
    }
}
