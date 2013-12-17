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

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class View extends \Magento\App\Action\Action
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
     * Check if gift registry is enabled on current store before all other actions
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     * @throws \Magento\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\GiftRegistry\Helper\Data')->isEnabled()) {
            throw new NotFoundException();
        }
        return parent::dispatch($request);
    }

    /**
     * View giftregistry list in 'My Account' section
     */
    public function indexAction()
    {
        $entity = $this->_objectManager->create('Magento\GiftRegistry\Model\Entity');
        $entity->loadByUrlKey($this->getRequest()->getParam('id'));
        if (!$entity->getId() || !$entity->getCustomerId() || !$entity->getTypeId() || !$entity->getIsActive()) {
            $this->_forward('noroute');
            return;
        }

        /** @var \Magento\Customer\Model\Customer */
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customer->load($entity->getCustomerId());
        $entity->setCustomer($customer);
        $this->_coreRegistry->register('current_entity', $entity);

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Gift Registry Info'));
        }
        $this->_view->renderLayout();
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
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');

        $success = false;

        try {
            $count = 0;
            foreach ($items as $itemId => $itemInfo) {
                $item = $this->_objectManager->create('Magento\GiftRegistry\Model\Item')->load($itemId);
                $optionCollection = $this->_objectManager->create('Magento\GiftRegistry\Model\Item\Option')->getCollection()
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
                $this->messageManager->addError(__('Please enter the quantity of items to add to cart.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We cannot add this item to your shopping cart.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        if (!$success) {
            $this->_redirect('*/*', array('_current' => true));
        } else {
            $this->_redirect('checkout/cart');
        }
    }
}
