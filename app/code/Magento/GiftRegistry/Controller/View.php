<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\RequestInterface;

/**
 * Gift registry frontend controller
 */
class View extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check if gift registry is enabled on current store before all other actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\App\Action\NotFoundException
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
     *
     * @return void
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
     *
     * @return void
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
                $optionCollection = $this->_objectManager->create(
                    'Magento\GiftRegistry\Model\Item\Option'
                )->getCollection()->addItemFilter(
                    $itemId
                );
                $item->setOptions($optionCollection->getOptionsByItem($item));
                if (!$item->getId() || $itemInfo['qty'] < 1 || $item->getQty() <= $item->getQtyFulfilled()) {
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
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We cannot add this item to your shopping cart.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        if (!$success) {
            $this->_redirect('*/*', array('_current' => true));
        } else {
            $this->_redirect('checkout/cart');
        }
    }
}
