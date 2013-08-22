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
 * Wishlist view block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Block_Info extends Magento_Wishlist_Block_Abstract
{
    /**
     * Create message block
     *
     * @return Magento_Core_Block_Abstract
     */
    public function getMessagesBlock()
    {
        return $this->getLayout()->getBlock('messages');
    }

    /**
     * Add form submission url
     *
     * @return string
     */
    public function getToCartUrl()
    {
        return $this->getUrl('wishlist/search/addtocart');
    }

    /**
     * Retrieve wishlist owner instance
     *
     * @return Magento_Customer_Model_Customer|null
     */
    public function getWishlistOwner()
    {
        $owner = Mage::getModel('Magento_Customer_Model_Customer');
        $owner->load($this->_getWishlist()->getCustomerId());
        return $owner;
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'wishlist/search/results',
            array('_query' => array('params' => Mage::getSingleton('Magento_Customer_Model_Session')->getLastWishlistSearchParams()))
        );
    }
}
