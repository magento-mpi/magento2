<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Model\Resource\Item\Collection;

use Magento\Core\Model\Layout\Argument\UpdaterInterface;

/**
 * Multiple wishlist item resource collection
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Updater implements UpdaterInterface
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
    public function __construct(\Magento\Wishlist\Helper\Data $wishlistData)
    {
        $this->_wishlistData = $wishlistData;
    }

    /**
     * Add filtration by customer id
     *
     * @param \Magento\Data\Collection\Db $argument
     * @return \Magento\Data\Collection\Db
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
