<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Index;

use Magento\Framework\Model\Exception;

class Cart extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * Add quote items to customer active gift registry
     *
     * @return void
     */
    public function execute()
    {
        $count = 0;
        try {
            $entity = $this->_initEntity('entity');
            if ($entity && $entity->getId()) {
                $skippedItems = 0;
                $request = $this->getRequest();
                if ($request->getParam('product')) {
                    //Adding from product page
                    $entity->addItem($request->getParam('product'), new \Magento\Framework\Object($request->getParams()));
                    $count = $request->getParam('qty') ? $request->getParam('qty') : 1;
                } else {
                    //Adding from cart
                    $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
                    foreach ($cart->getQuote()->getAllVisibleItems() as $item) {
                        if (!$this->_objectManager->get(
                            'Magento\GiftRegistry\Helper\Data'
                        )->canAddToGiftRegistry(
                            $item
                        )
                        ) {
                            $skippedItems++;
                            continue;
                        }
                        $entity->addItem($item);
                        $count += $item->getQty();
                        $cart->removeItem($item->getId());
                    }
                    $cart->save();
                }

                if ($count > 0) {
                    $this->messageManager->addSuccess(__('%1 item(s) have been added to the gift registry.', $count));
                } else {
                    $this->messageManager->addNotice(__('We have nothing to add to this gift registry.'));
                }
                if (!empty($skippedItems)) {
                    $this->messageManager->addNotice(
                        __("You can't add virtual products, digital products or gift cards to gift registries.")
                    );
                }
            }
        } catch (Exception $e) {
            if ($e->getCode() == \Magento\GiftRegistry\Model\Entity::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                $this->messageManager->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl('*/*'));
            } else {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('giftregistry');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Failed to add shopping cart items to gift registry.'));
        }

        if ($entity->getId()) {
            $this->_redirect('giftregistry/index/items', ['id' => $entity->getId()]);
        } else {
            $this->_redirect('giftregistry');
        }
    }
}
