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
 */
namespace Magento\MultipleWishlist\Block;

class Info extends \Magento\Wishlist\Block\AbstractBlock
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Customer model factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerFactory = $customerFactory;
        parent::__construct($coreRegistry, $wishlistData, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Create message block
     *
     * @return \Magento\Core\Block\AbstractBlock
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
     * @return \Magento\Customer\Model\Customer|null
     */
    public function getWishlistOwner()
    {
        /** @var \Magento\Customer\Model\Customer $owner */
        $owner = $this->_customerFactory->create();
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
            array('_query' => array('params' => $this->_customerSession->getLastWishlistSearchParams()))
        );
    }
}
