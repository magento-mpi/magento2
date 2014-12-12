<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Controller\Cart;

class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * Add Gift Card to current quote
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                if (strlen($code) > \Magento\GiftCardAccount\Helper\Data::GIFT_CARD_CODE_MAX_LENGTH) {
                    throw new \Magento\Framework\Model\Exception(__('Please correct the gift card code.'));
                }
                $this->_objectManager->create(
                    'Magento\GiftCardAccount\Model\Giftcardaccount'
                )->loadByCode(
                    $code
                )->addToCart();
                $this->messageManager->addSuccess(
                    __('Gift Card "%1" was added.', $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($code))
                );
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We cannot apply this gift card.'));
            }
        }
        $this->_redirect('checkout/cart');
    }
}
