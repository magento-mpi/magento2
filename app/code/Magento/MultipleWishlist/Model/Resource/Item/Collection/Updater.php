<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multiple wishlist item resource collection
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Model\Resource\Item\Collection;

class Updater
    implements \Magento\Core\Model\Layout\Argument\UpdaterInterface
{
    /**
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Magento_Wishlist_Helper_Data $wishlistData
     */
    public function __construct(
        Magento_Wishlist_Helper_Data $wishlistData
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
