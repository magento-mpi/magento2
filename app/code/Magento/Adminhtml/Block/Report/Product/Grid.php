<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml products report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Report\Product;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Reports\Model\Resource\Product\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Reports\Model\Resource\Product\CollectionFactory $collectionFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\Reports\Model\Resource\Product\CollectionFactory $collectionFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsReportGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {

        $collection = $this->_collectionFactory->create();
        $collection->getEntity()->setStore(0);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        $totalObj = new \Magento\Reports\Model\Totals();
        $this->setTotals($totalObj->countTotals($this));
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id',
            'total'     =>'Total'
        ));

        $this->addColumn('name', array(
            'header'    =>__('Name'),
            'index'     =>'name'
        ));

        $this->addColumn('viewed', array(
            'header'    =>__('Viewed'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'viewed',
            'total'     =>'sum'
        ));

        $this->addColumn('added', array(
            'header'    =>__('Added'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'added',
            'total'     =>'sum'
        ));

        $this->addColumn('purchased', array(
            'header'    =>__('Purchased'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'purchased',
            'total'     =>'sum'
        ));

        $this->addColumn('fulfilled', array(
            'header'    =>__('Fulfilled'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'fulfilled',
            'total'     =>'sum'
        ));

        $this->addColumn('revenue', array(
            'header'    =>__('Revenue'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'revenue',
            'total'     =>'sum'
        ));

        $this->setCountTotals(true);

        $this->addExportType('*/*/exportProductsCsv', __('CSV'));
        $this->addExportType('*/*/exportProductsExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

}

