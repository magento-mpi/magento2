<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller\Onepage;

class SaveShipping extends \Magento\Checkout\Controller\Onepage
{
    /**
     * Shipping address save action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost() || $this->_expireAjax()) {
            return;
        }
        $data = $this->getRequest()->getPost('shipping', []);
        $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
        $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

        $quote = $this->getOnepage()->getQuote();
        if (!isset($result['error'])) {
            if (!$quote->validateMinimumAmount()) {
                $result = [
                    'error' => -1,
                    'message' => $this->scopeConfig->getValue(
                        'sales/minimum_order/error_message',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $quote->getStoreId()
                    )
                ];
            } else {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = [
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                ];
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
        );
    }
}
