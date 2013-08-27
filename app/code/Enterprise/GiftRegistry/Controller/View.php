<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry frontend controller
 */
class Enterprise_GiftRegistry_Controller_View extends Magento_Core_Controller_Front_Action
{
    /**
     * Check if gift registry is enabled on current store before all other actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Enterprise_GiftRegistry_Helper_Data')->isEnabled()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
    }

    /**
     * View giftregistry list in 'My Account' section
     */
    public function indexAction()
    {
        $entity = Mage::getModel('Enterprise_GiftRegistry_Model_Entity');
        $entity->loadByUrlKey($this->getRequest()->getParam('id'));
        if (!$entity->getId() || !$entity->getCustomerId() || !$entity->getTypeId() || !$entity->getIsActive()) {
            $this->_forward('noroute');
            return;
        }

        /** @var Magento_Customer_Model_Customer */
        $customer = Mage::getModel('Magento_Customer_Model_Customer');
        $customer->load($entity->getCustomerId());
        $entity->setCustomer($customer);
        Mage::register('current_entity', $entity);

        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Gift Registry Info'));
        }
        $this->renderLayout();
    }

    /**
     * Add specified gift registry items to quote
     */
    public function addToCartAction()
    {
        $items = $this->getRequest()->getParam('items');
        if (!$items) {
            $this->_redirect('*/*', array('_current' => true));
            return;
        }
        /* @var Magento_Checkout_Model_Cart */
        $cart = Mage::getSingleton('Magento_Checkout_Model_Cart');
        /* @var $session Magento_Core_Model_Session_Generic */
        $session    = Mage::getSingleton('Magento_Customer_Model_Session');
        $success = false;

        try {
            $count = 0;
            foreach ($items as $itemId => $itemInfo) {
                $item = Mage::getModel('Enterprise_GiftRegistry_Model_Item')->load($itemId);
                $optionCollection = Mage::getModel('Enterprise_GiftRegistry_Model_Item_Option')->getCollection()
                    ->addItemFilter($itemId);
                $item->setOptions($optionCollection->getOptionsByItem($item));
                if (!$item->getId() || $itemInfo['qty'] < 1 || ($item->getQty() <= $item->getQtyFulfilled())) {
                    continue;
                }
                $item->addToCart($cart, $itemInfo['qty']);
                $count += $itemInfo['qty'];
            }
            $cart->save()->getQuote()->collectTotals();
            $success = true;
            if (!$count) {
                $success = false;
                $session->addError(__('Please enter the quantity of items to add to cart.'));
            }
        } catch (Magento_Core_Exception $e) {
            $session->addError(__($e->getMessage()));
        } catch (Exception $e) {
            $session->addException($e, __('We cannot add this item to your shopping cart.'));
            Mage::logException($e);
        }
        if (!$success) {
            $this->_redirect('*/*', array('_current' => true));
        } else {
            $this->_redirect('checkout/cart');
        }
    }
}
