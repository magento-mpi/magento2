<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Model\Observer;

use Magento\AdvancedCheckout\Model\Cart;

class AddBySku
{
    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * @var CartProvider
     */
    protected $cartProvider;

    /**
     * @param Cart $cart
     * @param CartProvider $backendCartProvider
     */
    public function __construct(
        Cart $cart,
        CartProvider $backendCartProvider
    ) {
        $this->_cart = $cart;
        $this->cartProvider = $backendCartProvider;
    }

    /**
     * Check submitted SKU's form the form or from error grid
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $request \Magento\Framework\App\RequestInterface */
        $request = $observer->getRequestModel();
        $cart = $this->cartProvider->get($observer);

        if (empty($request) || empty($cart)) {
            return;
        }

        $removeFailed = $request->getPost('sku_remove_failed');

        if ($removeFailed || $request->getPost('from_error_grid')) {
            $cart->removeAllAffectedItems();
            if ($removeFailed) {
                return;
            }
        }

        $sku = $observer->getRequestModel()->getPost('remove_sku', false);

        if ($sku) {
            $this->cartProvider->get($observer)->removeAffectedItem($sku);
            return;
        }

        $addBySkuItems = $request->getPost(
            \Magento\AdvancedCheckout\Block\Adminhtml\Sku\AbstractSku::LIST_TYPE,
            []
        );
        $items = $request->getPost('item', []);
        if (!$addBySkuItems) {
            return;
        }
        foreach ($addBySkuItems as $id => $params) {
            $sku = (string) (isset($params['sku']) ? $params['sku'] : $id);
            $cart->prepareAddProductBySku($sku, $params['qty'], isset($items[$id]) ? $items[$id] : []);
        }
        /* @var $orderCreateModel \Magento\Sales\Model\AdminOrder\Create */
        $orderCreateModel = $observer->getOrderCreateModel();
        $cart->saveAffectedProducts($orderCreateModel, false);
        // We have already saved succeeded add by SKU items in saveAffectedItems(). This prevents from duplicate saving.
        $request->setPost('item', []);
    }
}
