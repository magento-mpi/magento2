<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\View;

class AddToCart extends \Magento\GiftRegistry\Controller\View
{
    /**
     * Add specified gift registry items to quote
     *
     * @return void
     */
    public function execute()
    {
        $items = $this->getRequest()->getParam('items');
        if (!$items) {
            $this->_redirect('*/*', array('_current' => true));
            return;
        }
        /* @var \Magento\Checkout\Model\Cart */
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');

        $success = false;

        try {
            $count = 0;
            foreach ($items as $itemId => $itemInfo) {
                $item = $this->_objectManager->create('Magento\GiftRegistry\Model\Item')->load($itemId);
                $optionCollection = $this->_objectManager->create(
                    'Magento\GiftRegistry\Model\Item\Option'
                )->getCollection()->addItemFilter(
                    $itemId
                );
                $item->setOptions($optionCollection->getOptionsByItem($item));
                if (!$item->getId() || $itemInfo['qty'] < 1 || $item->getQty() <= $item->getQtyFulfilled()) {
                    continue;
                }
                $item->addToCart($cart, $itemInfo['qty']);
                $count += $itemInfo['qty'];
            }
            $cart->save()->getQuote()->collectTotals();
            $success = true;
            if (!$count) {
                $success = false;
                $this->messageManager->addError(__('Please enter the quantity of items to add to cart.'));
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We cannot add this item to your shopping cart.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        if (!$success) {
            $this->_redirect('*/*', array('_current' => true));
        } else {
            $this->_redirect('checkout/cart');
        }
    }
}
