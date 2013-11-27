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
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $coreData, $data);
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
}
