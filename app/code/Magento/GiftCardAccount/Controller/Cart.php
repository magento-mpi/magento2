<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Controller;

class Cart extends \Magento\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * No index action, forward to 404
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('noroute');
    }

    /**
     * Add Gift Card to current quote
     *
     * @return void
     */
    public function addAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                if (strlen($code) > \Magento\GiftCardAccount\Helper\Data::GIFT_CARD_CODE_MAX_LENGTH) {
                    throw new \Magento\Core\Exception(__('Please correct the gift card code.'));
                }
                $this->_objectManager->create('Magento\GiftCardAccount\Model\Giftcardaccount')
                    ->loadByCode($code)
                    ->addToCart();
                $this->messageManager->addSuccess(
                    __('Gift Card "%1" was added.', $this->_objectManager->get('Magento\Escaper')->escapeHtml($code))
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We cannot apply this gift card.'));
            }
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * @return void
     */
    public function removeAction()
    {
        $code = $this->getRequest()->getParam('code');
        if ($code) {
            try {
                $this->_objectManager->create('Magento\GiftCardAccount\Model\Giftcardaccount')
                    ->loadByCode($code)
                    ->removeFromCart();
                $this->messageManager->addSuccess(
                    __('Gift Card "%1" was removed.', $this->_objectManager->get('Magento\Escaper')->escapeHtml($code))
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We cannot remove this gift card.'));
            }
            $this->_redirect('checkout/cart');
        } else {
            $this->_forward('noroute');
        }
    }

    /**
     * Check a gift card account availability
     *
     * @return void
     */
    public function quickCheckAction()
    {
        /* @var $card \Magento\GiftCardAccount\Model\Giftcardaccount */
        $card = $this->_objectManager->create('Magento\GiftCardAccount\Model\Giftcardaccount')
            ->loadByCode($this->getRequest()->getParam('giftcard_code', ''));
        $this->_coreRegistry->register('current_giftcardaccount', $card);
        try {
            $card->isValid(true, true, true, false);
        } catch (\Magento\Core\Exception $e) {
            $card->unsetData();
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
