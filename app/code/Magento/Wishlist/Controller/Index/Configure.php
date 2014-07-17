<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wishlist\Controller\Index;

use Magento\Wishlist\Controller\IndexInterface;
use Magento\Framework\App\Action;
use Magento\Framework\App\Action\NotFoundException;

class Configure extends Action\Action implements IndexInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @param Action\Context $context
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Action to reconfigure wishlist item
     *
     * @return void
     * @throws NotFoundException
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        try {
            /* @var $item \Magento\Wishlist\Model\Item */
            $item = $this->_objectManager->create('Magento\Wishlist\Model\Item');
            $item->loadWithOptions($id);
            if (!$item->getId()) {
                throw new \Magento\Framework\Model\Exception(__('We can\'t load the wish list item.'));
            }
            $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
            if (!$wishlist) {
                throw new NotFoundException();
            }

            $this->_coreRegistry->register('wishlist_item', $item);

            $params = new \Magento\Framework\Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);
            $buyRequest = $item->getBuyRequest();
            if (!$buyRequest->getQty() && $item->getQty()) {
                $buyRequest->setQty($item->getQty());
            }
            if ($buyRequest->getQty() && !$item->getQty()) {
                $item->setQty($buyRequest->getQty());
                $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            }
            $params->setBuyRequest($buyRequest);
            $this->_objectManager->get(
                'Magento\Catalog\Helper\Product\View'
            )->prepareAndRender(
                $item->getProductId(),
                $this,
                $params
            );
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*');
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t configure the product.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->_redirect('*');
            return;
        }
    }
}
