<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml wishlist report page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Wishlist extends Mage_Adminhtml_Block_Template
{

    public $wishlists_count;
    public $items_bought;
    public $shared_count;
    public $referrals_count;
    public $conversions_count;
    public $customer_with_wishlist;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('report/wishlist.phtml');
    }

    public function _beforeToHtml()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Wishlist_Grid', 'report.grid')
        );

        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Wishlist_Collection');

        list($customerWithWishlist, $wishlistsCount) = $collection->getWishlistCustomerCount();
        $this->setCustomerWithWishlist($customerWithWishlist);
        $this->setWishlistsCount($wishlistsCount);
        $this->setItemsBought(0);
        $this->setSharedCount($collection->getSharedCount());
        $this->setReferralsCount(0);
        $this->setConversionsCount(0);

        return $this;
    }

}
