<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard recent orders grid
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Dashboard\Orders;

class Grid extends \Magento\Backend\Block\Dashboard\Grid
{
    /**
     * @var \Magento\Reports\Model\Resource\Order\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Reports\Model\Resource\Order\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Reports\Model\Resource\Order\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $coreData, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('lastOrdersGrid');
    }

    protected function _prepareCollection()
    {
        if (!$this->_coreData->isModuleEnabled('Magento_Reports')) {
            return $this;
        }
        $collection = $this->_collectionFactory->create()
            ->addItemCountExpr()
            ->joinCustomerName('customer')
            ->orderByCreatedAt();

        if ($this->getParam('store') || $this->getParam('website') || $this->getParam('group')) {
            if ($this->getParam('store')) {
                $collection->addAttributeToFilter('store_id', $this->getParam('store'));
            } else if ($this->getParam('website')) {
                $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
                $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
            } else if ($this->getParam('group')) {
                $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
                $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
            }

            $collection->addRevenueToSelect();
        } else {
            $collection->addRevenueToSelect(true);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepares page sizes for dashboard grid with las 5 orders
     *
     * @return void
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
//        Remove count of total orders
//        $this->getCollection()->setCurPage($this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer', array(
            'header'    => __('Customer'),
            'sortable'  => false,
            'index'     => 'customer',
            'default'   => __('Guest'),
        ));

        $this->addColumn('items', array(
            'header'    => __('Items'),
            'align'     => 'right',
            'type'      => 'number',
            'sortable'  => false,
            'index'     => 'items_count'
        ));

        $baseCurrencyCode = $this->_storeManager->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('total', array(
            'header'    => __('Grand Total'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'revenue'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('sales/order/view', array('order_id'=>$row->getId()));
    }
}
