<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller\Cart;

class Add extends \Magento\Checkout\Controller\Cart
{
    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product || false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get('Magento\Framework\StoreManagerInterface')->getStore()->getId();
            $product = $this->_objectManager->create(
                'Magento\Catalog\Model\Product'
            )->setStoreId(
                $storeId
            )->load(
                $productId
            );
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    /**
     * Add product to shopping cart action
     *
     * @return void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    array('locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');
            if ($product->getTypeId() === 'giftcard') {
                foreach ($params as $key => $value) {
                    if ($value) {
                        $this->_checkoutSession->setData($key, $value);
                    }
                }
            }

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();

            $this->_checkoutSession->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($product->getName())
                    );
                    $this->messageManager->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice(
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError(
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($cartUrl));
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We cannot add this item to your shopping cart'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->_goBack();
        }
    }
}
