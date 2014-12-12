<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Controller\Cart;

class Remove extends \Magento\Framework\App\Action\Action
{
    /**
     * @return void
     */
    public function execute()
    {
        $code = $this->getRequest()->getParam('code');
        if ($code) {
            try {
                $this->_objectManager->create(
                    'Magento\GiftCardAccount\Model\Giftcardaccount'
                )->loadByCode(
                    $code
                )->removeFromCart();
                $this->messageManager->addSuccess(
                    __('Gift Card "%1" was removed.', $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($code))
                );
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We cannot remove this gift card.'));
            }
            $this->_redirect('checkout/cart');
        } else {
            $this->_forward('noroute');
        }
    }
}
