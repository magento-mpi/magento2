<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Controller_Customer extends Magento_Core_Controller_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this)) {
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
                if (!$this->_objectManager->get('Magento_CustomerBalance_Helper_Data')->isEnabled()) {
                    Mage::throwException(__("You can't redeem a gift card now."));
                }
                Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')->loadByCode($code)
                    ->setIsRedeemed(true)->redeem();
                Mage::getSingleton('Magento_Customer_Model_Session')->addSuccess(
                    __('Gift Card "%1" was redeemed.', $this->_objectManager->get('Magento_Core_Helper_Data')->escapeHtml($code))
                );
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Customer_Model_Session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Customer_Model_Session')->addException($e, __('We cannot redeem this gift card.'));
            }
            $this->_redirect('*/*/*');
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $this->loadLayoutUpdates();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Gift Card'));
        }
        $this->renderLayout();
    }
}
