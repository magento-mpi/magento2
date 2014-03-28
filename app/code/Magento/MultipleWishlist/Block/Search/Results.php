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
 * Multiple wishlist search results
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block\Search;

class Results extends \Magento\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Customer\Helper\View $customerViewHelper,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_customerViewHelper = $customerViewHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve wishlist search results
     *
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function getSearchResults()
    {
        return $this->_coreRegistry->registry('search_results');
    }

    /**
     * Return frontend registry link
     *
     * @param \Magento\Wishlist\Model\Wishlist $item
     * @return string
     */
    public function getWishlistLink(\Magento\Wishlist\Model\Wishlist $item)
    {
        return $this->getUrl('*/search/view', array('wishlist_id' => $item->getId()));
    }

    public function getCustomerName($customerId)
    {

    }
}
