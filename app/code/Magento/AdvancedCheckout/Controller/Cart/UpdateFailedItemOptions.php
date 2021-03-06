<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Context;

class UpdateFailedItemOptions extends \Magento\AdvancedCheckout\Controller\Cart
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
    }

    /**
     * Update failed items options data and add it to cart
     *
     * @return void
     */
    public function execute()
    {
        $hasError = false;
        $id = (int)$this->getRequest()->getParam('id');
        $buyRequest = new \Magento\Framework\Object($this->getRequest()->getParams());
        try {
            $cart = $this->_getCart();

            $product = $this->productRepository->getById(
                $id,
                false,
                $this->_objectManager->get('Magento\Store\Model\StoreManager')->getStore()->getId()
            );

            $cart->addProduct($product, $buyRequest)->save();

            $this->_getFailedItemsCart()->removeAffectedItem($this->getRequest()->getParam('sku'));

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $productName = $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($product->getName());
                    $message = __('You added %1 to your shopping cart.', $productName);
                    $this->messageManager->addSuccess($message);
                }
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $hasError = true;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('You cannot add a product.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $hasError = true;
        }

        if ($hasError) {
            $this->_redirect('checkout/cart/configureFailed', ['id' => $id, 'sku' => $buyRequest->getSku()]);
        } else {
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Get checkout session model instance
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getSession()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Session');
    }

    /**
     * Get cart model instance
     *
     * @return \Magento\Checkout\Model\Cart
     */
    protected function _getCart()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Cart');
    }
}
