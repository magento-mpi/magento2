<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action;

class Add extends \Magento\Wishlist\Controller\Index\Add
{
    /**
     * @var \Magento\MultipleWishlist\Model\WishlistEditor
     */
    protected $wishlistEditor;

    /**
     * @param Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor
     */
    public function __construct(
        Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor
    ) {
        $this->wishlistEditor = $wishlistEditor;
        parent::__construct($context, $customerSession, $wishlistProvider, $productRepository);
    }

    /**
     * Add item to wishlist
     * Create new wishlist if wishlist params (name, visibility) are provided
     *
     * @return void
     */
    public function execute()
    {
        $customerId = $this->_customerSession->getCustomerId();
        $name = $this->getRequest()->getParam('name');
        $visibility = $this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0;
        if ($name !== null) {
            try {
                $wishlist = $this->wishlistEditor->edit($customerId, $name, $visibility);
                $this->messageManager->addSuccess(
                    __(
                        'Wish List "%1" was saved.',
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($wishlist->getName())
                    )
                );
                $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong creating the wish list.'));
            }
        }
        parent::execute();
    }
}
