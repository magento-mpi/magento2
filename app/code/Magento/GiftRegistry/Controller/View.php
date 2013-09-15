<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry frontend controller
 */
namespace Magento\GiftRegistry\Controller;

class View extends \Magento\Core\Controller\Front\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check if gift registry is enabled on current store before all other actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Magento\GiftRegistry\Helper\Data')->isEnabled()) {
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
        $entity = \Mage::getModel('Magento\GiftRegistry\Model\Entity');
        $entity->loadByUrlKey($this->getRequest()->getParam('id'));
        if (!$entity->getId() || !$entity->getCustomerId() || !$entity->getTypeId() || !$entity->getIsActive()) {
            $this->_forward('noroute');
            return;
        }

        /** @var \Magento\Customer\Model\Customer */
        $customer = \Mage::getModel('Magento\Customer\Model\Customer');
        $customer->load($entity->getCustomerId());
        $entity->setCustomer($customer);
        $this->_coreRegistry->register('current_entity', $entity);

        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Customer\Model\Session');
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
        /* @var \Magento\Checkout\Model\Cart */
        $cart = \Mage::getSingleton('Magento\Checkout\Model\Cart');
        /* @var $session \Magento\Core\Model\Session\Generic */
        $session    = \Mage::getSingleton('Magento\Customer\Model\Session');
        $success = false;

        try {
            $count = 0;
            foreach ($items as $itemId => $itemInfo) {
                $item = \Mage::getModel('Magento\GiftRegistry\Model\Item')->load($itemId);
                $optionCollection = \Mage::getModel('Magento\GiftRegistry\Model\Item\Option')->getCollection()
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
        } catch (\Magento\Core\Exception $e) {
            $session->addError(__($e->getMessage()));
        } catch (\Exception $e) {
            $session->addException($e, __('We cannot add this item to your shopping cart.'));
            \Mage::logException($e);
        }
        if (!$success) {
            $this->_redirect('*/*', array('_current' => true));
        } else {
            $this->_redirect('checkout/cart');
        }
    }
}
