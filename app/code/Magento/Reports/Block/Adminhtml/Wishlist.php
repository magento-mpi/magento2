<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml wishlist report page content block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Adminhtml;

class Wishlist extends \Magento\Backend\Block\Template
{
    /**
     * @var
     */
    public $wishlists_count;

    /**
     * @var
     */
    public $items_bought;

    /**
     * @var
     */
    public $shared_count;

    /**
     * @var
     */
    public $referrals_count;

    /**
     * @var
     */
    public $conversions_count;

    /**
     * @var
     */
    public $customer_with_wishlist;

    /**
     * @var string
     */
    protected $_template = 'report/wishlist.phtml';

    /**
     * Reports wishlist collection factory
     *
     * @var \Magento\Reports\Model\Resource\Wishlist\CollectionFactory
     */
    protected $_wishlistFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Reports\Model\Resource\Wishlist\CollectionFactory $wishlistFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reports\Model\Resource\Wishlist\CollectionFactory $wishlistFactory,
        array $data = array()
    ) {
        $this->_wishlistFactory = $wishlistFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Magento\Reports\Block\Adminhtml\Wishlist\Grid', 'report.grid')
        );

        $collection = $this->_wishlistFactory->create();

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
