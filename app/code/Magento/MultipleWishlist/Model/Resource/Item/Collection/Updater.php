<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Model\Resource\Item\Collection;

class Updater implements \Magento\View\Layout\Argument\UpdaterInterface
{
    /**
     * Wishlist data
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     */
    public function __construct(
        \Magento\Wishlist\Helper\Data $wishlistData
    ) {
        $this->_wishlistData = $wishlistData;
    }

    /**
     * Add filtration by customer id
     *
     * @param \Magento\Data\Collection\Db $argument
     * @return mixed
     */
    public function update($argument)
    {
        $adapter = $argument->getConnection();
        $defaultWishlistName = $this->_wishlistData->getDefaultWishlistName();
        $argument->getSelect()->columns(
            array('wishlist_name' => $adapter->getIfNullSql('wishlist.name', $adapter->quote($defaultWishlistName)))
        );

        $argument->addFilterToMap(
            'wishlist_name', $adapter->getIfNullSql('wishlist.name', $adapter->quote($defaultWishlistName))
        );
        return $argument;
    }
}
