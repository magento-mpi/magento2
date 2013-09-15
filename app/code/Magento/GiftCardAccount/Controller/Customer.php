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

class Customer extends \Magento\Core\Controller\Front\Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
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
                    \Mage::throwException(__("You can't redeem a gift card now."));
                }
                \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount')->loadByCode($code)
                    ->setIsRedeemed(true)->redeem();
                \Mage::getSingleton('Magento\Customer\Model\Session')->addSuccess(
                    __('Gift Card "%1" was redeemed.', $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($code))
                );
            } catch (\Magento\Core\Exception $e) {
                \Mage::getSingleton('Magento\Customer\Model\Session')->addError($e->getMessage());
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Customer\Model\Session')->addException($e, __('We cannot redeem this gift card.'));
            }
            $this->_redirect('*/*/*');
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Customer\Model\Session');
        $this->loadLayoutUpdates();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Gift Card'));
        }
        $this->renderLayout();
    }
}
