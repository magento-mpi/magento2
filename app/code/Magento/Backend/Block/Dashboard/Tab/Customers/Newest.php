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
 * Adminhtml dashboard most recent customers grid
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Dashboard\Tab\Customers;

class Newest extends \Magento\Backend\Block\Dashboard\Grid
{
    /**
     * @var \Magento\Reports\Model\Resource\Customer\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Reports\Model\Resource\Customer\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Reports\Model\Resource\Customer\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('customersNewestGrid');
    }

    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create()
            ->addCustomerName();

        $storeFilter = 0;
        if ($this->getParam('store')) {
            $collection->addAttributeToFilter('store_id', $this->getParam('store'));
            $storeFilter = 1;
        } elseif ($this->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
        } elseif ($this->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
        }

        $collection->addOrdersStatistics($storeFilter)
            ->orderByCustomerRegistration();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => __('Customer'),
            'sortable'  => false,
            'index'     => 'name'
        ));

        $this->addColumn('orders_count', array(
            'header'    => __('Orders'),
            'sortable'  => false,
            'index'     => 'orders_count',
            'type'      => 'number'
        ));

        $baseCurrencyCode = (string) $this->_storeManager->getStore((int)$this->getParam('store'))
            ->getBaseCurrencyCode();

        $this->addColumn('orders_avg_amount', array(
            'header'    => __('Average'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_avg_amount',
            'renderer'  =>'Magento\Reports\Block\Adminhtml\Grid\Column\Renderer\Currency'
        ));

        $this->addColumn('orders_sum_amount', array(
            'header'    => __('Total'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_sum_amount',
            'renderer'  =>'Magento\Reports\Block\Adminhtml\Grid\Column\Renderer\Currency'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('customer/index/edit', array('id'=>$row->getId()));
    }
}
