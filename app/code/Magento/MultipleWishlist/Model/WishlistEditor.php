<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Model;

class WishlistEditor
{
    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory
     */
    protected $wishlistColFactory;

    /**
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistColFactory
     * @param \Magento\MultipleWishlist\Helper\Data $helper
     */
    public function __construct(
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistColFactory,
        \Magento\MultipleWishlist\Helper\Data $helper
    ) {
        $this->wishlistFactory = $wishlistFactory;
        $this->customerSession = $customerSession;
        $this->wishlistColFactory = $wishlistColFactory;
        $this->helper = $helper;
    }

    /**
     * Edit wishlist
     *
     * @param int $customerId
     * @param string $wishlistName
     * @param bool $visibility
     * @param int $wishlistId
     * @return \Magento\Wishlist\Model\Wishlist
     * @throws \Magento\Framework\Model\Exception
     */
    public function edit($customerId, $wishlistName, $visibility = false, $wishlistId = null)
    {
        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->wishlistFactory->create();

        if (!$customerId) {
            throw new \Magento\Framework\Model\Exception(__('Log in to edit wish lists.'));
        }
        if (!strlen($wishlistName)) {
            throw new \Magento\Framework\Model\Exception(__('Provide wish list name'));
        }
        if ($wishlistId) {
            $wishlist->load($wishlistId);
            if ($wishlist->getCustomerId() !== $this->customerSession->getCustomerId()) {
                throw new \Magento\Framework\Model\Exception(
                    __('The wish list is not assigned to your account and cannot be edited.')
                );
            }
        } else {
            /** @var \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlistCollection */
            $wishlistCollection = $this->wishlistColFactory->create();
            $wishlistCollection->filterByCustomerId($customerId);
            $limit = $this->helper->getWishlistLimit();
            if ($this->helper->isWishlistLimitReached($wishlistCollection)) {
                throw new \Magento\Framework\Model\Exception(__('Only %1 wish lists can be created.', $limit));
            }
            $wishlist->setCustomerId($customerId);
        }
        $wishlist->setName($wishlistName)->setVisibility($visibility)->generateSharingCode()->save();
        return $wishlist;
    }
}
