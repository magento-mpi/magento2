<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Enterprise checkout index controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Controller;

class Sku extends \Magento\Framework\App\Action\Action
{
    /**
     * Check functionality is enabled and applicable to the Customer
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        // guest redirected to "Login or Create an Account" page
        /** @var $customerSession \Magento\Customer\Model\Session */
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        if (!$customerSession->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
            return parent::dispatch($request);
        }

        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_objectManager->get('Magento\AdvancedCheckout\Helper\Data');
        if (!$helper->isSkuEnabled() || !$helper->isSkuApplied()) {
            return $this->_redirect('customer/account');
        }
        return parent::dispatch($request);
    }
}
