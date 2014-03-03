<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Block\Adminhtml\Manage\Accordion;

/**
 * Accordion grid for products in wishlist
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Wishlist
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\Wishlist
{
    /**
     * Item collection factory
     *
     * @var \Magento\MultipleWishlist\Model\Resource\Item\Collection
     */
    protected $_itemCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Data\CollectionFactory $collectionFactory
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     * @param \Magento\MultipleWishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Data\CollectionFactory $collectionFactory,
        \Magento\Registry $coreRegistry,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\MultipleWishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory,
        array $data = array()
    ) {
        $this->_itemCollectionFactory = $itemCollectionFactory;
        parent::__construct(
            $context,
            $backendHelper,
            $collectionFactory,
            $coreRegistry,
            $itemFactory,
            $data
        );
    }

    /**
     * Return items collection
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    protected function _createItemsCollection()
    {
        return $this->_itemCollectionFactory->create();
    }

    /**
     * Prepare Grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('wishlist_name', array(
            'header'    => __('Wishlist'),
            'index'     => 'wishlist_name',
            'sortable'  => false
        ));

        return parent::_prepareColumns();
    }
}
