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

class Remove extends Action\Action implements IndexInterface
{
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @param Action\Context $context
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     */
    public function __construct(
        Action\Context $context,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
    ) {
        $this->wishlistProvider = $wishlistProvider;
        parent::__construct($context);
    }

    /**
     * Remove item
     *
     * @return void
     * @throws NotFoundException
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('item');
        $item = $this->_objectManager->create('Magento\Wishlist\Model\Item')->load($id);
        if (!$item->getId()) {
            throw new NotFoundException();
        }
        $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
        if (!$wishlist) {
            throw new NotFoundException();
        }
        try {
            $item->delete();
            $wishlist->save();
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError(
                __('An error occurred while deleting the item from wish list: %1', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred while deleting the item from wish list.'));
        }

        $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();

        $url = $this->_redirect->getRedirectUrl($this->_url->getUrl('*/*'));
        $this->getResponse()->setRedirect($url);
    }
}
