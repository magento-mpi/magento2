<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Item Attributes Grid Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Item\Attribute;

class Grid
    extends \Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid
{
    /**
     * @var \Magento\Rma\Model\Resource\Item\Attribute\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Rma\Model\Resource\Item\Attribute\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Rma\Model\Resource\Item\Attribute\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Initialize grid, set grid Id
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rmaItemAttributeGrid');
        $this->setDefaultSort('sort_order');
    }

    /**
     * Prepare customer attributes grid collection object
     *
     * @return \Magento\Customer\Block\Adminhtml\Customer\Attribute\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Rma\Model\Resource\Item\Attribute\Collection */
        $collection = $this->_collectionFactory->create();
        $collection->addSystemHiddenFilter()->addExcludeHiddenFrontendFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare customer attributes grid columns
     *
     * @return \Magento\Customer\Block\Adminhtml\Customer\Attribute\Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('is_visible', array(
            'header'    => __('Visible to Customer'),
            'sortable'  => true,
            'index'     => 'is_visible',
            'type'      => 'options',
            'options'   => array(
                '0' => __('No'),
                '1' => __('Yes'),
            ),
            'header_css_class'  => 'col-visible-on-front',
            'column_css_class'  => 'col-visible-on-front'
        ));

        $this->addColumn('sort_order', array(
            'header'    => __('Sort Order'),
            'sortable'  => true,
            'align'     => 'center',
            'index'     => 'sort_order',
            'header_css_class'  => 'col-order',
            'column_css_class'  => 'col-order'
        ));

        return $this;
    }
}
