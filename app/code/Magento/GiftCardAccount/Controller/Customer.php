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

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Customer extends \Magento\App\Action\Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
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
     */
    public function indexAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                if (!$this->_objectManager->get('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
                    throw new \Magento\Core\Exception(__("You can't redeem a gift card now."));
                }
                $this->_objectManager->create('Magento\GiftCardAccount\Model\Giftcardaccount')
                    ->loadByCode($code)
                    ->setIsRedeemed(true)->redeem();
                $this->messageManager->addSuccess(
                    __('Gift Card "%1" was redeemed.', $this->_objectManager->get('Magento\Escaper')->escapeHtml($code))
                );
            } catch (\Magento\Core\Exception $e) {
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
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Gift Card'));
        }
        $this->_view->renderLayout();
    }
}
