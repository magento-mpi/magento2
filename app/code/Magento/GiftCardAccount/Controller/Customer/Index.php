<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Controller\Customer;

use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Redeem gift card
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                if (!$this->_objectManager->get('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
                    throw new \Magento\Framework\Model\Exception(__("You can't redeem a gift card now."));
                }
                $this->_objectManager->create(
                    'Magento\GiftCardAccount\Model\Giftcardaccount'
                )->loadByCode(
                    $code
                )->setIsRedeemed(
                    true
                )->redeem();
                $this->messageManager->addSuccess(
                    __(
                        'Gift Card "%1" was redeemed.',
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($code)
                    )
                );
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We cannot redeem this gift card.'));
            }
            $this->_redirect('*/*/*');
            return;
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->loadLayoutUpdates();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Gift Card'));
        $this->_view->renderLayout();
    }
}
