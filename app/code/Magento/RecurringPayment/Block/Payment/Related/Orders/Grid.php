<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Payment\Related\Orders;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Recurring payment related orders grid
 *
 * @method \Magento\Object[] getGridElements()
 * @method Grid setGridElements($orders)
 */
class Grid extends \Magento\RecurringPayment\Block\Payment\View
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Collection
     */
    protected $_orderCollection;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_config;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @var \Magento\RecurringPayment\Model\Resource\Order\CollectionFilter
     */
    protected $_recurringCollectionFilter;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Sales\Model\Resource\Order\Collection $collection
     * @param \Magento\Sales\Model\Order\Config $config
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\RecurringPayment\Model\Resource\Order\CollectionFilter $recurringCollectionFilter
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Sales\Model\Resource\Order\Collection $collection,
        \Magento\Sales\Model\Order\Config $config,
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\RecurringPayment\Model\Resource\Order\CollectionFilter $recurringCollectionFilter,
        array $data = array()
    ) {
        $this->_coreHelper = $coreHelper;
        parent::__construct($context, $registry, $data);
        $this->_orderCollection = $collection;
        $this->_config = $config;
        $this->_isScopePrivate = true;
        $this->_recurringCollectionFilter = $recurringCollectionFilter;
    }

    /**
     * Prepare related orders collection
     *
     * @param array|string $fieldsToSelect
     * @return void
     */
    protected function _prepareRelatedOrders($fieldsToSelect = '*')
    {
        if (null === $this->_relatedOrders) {
            $this->_orderCollection
                ->addFieldToSelect($fieldsToSelect)
                ->addFieldToFilter('customer_id', $this->_registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID))
                ->setOrder('entity_id', 'desc');
            $this->_relatedOrders = $this->_recurringCollectionFilter->byIds(
                $this->_orderCollection,
                $this->_recurringPayment->getId()
            );
        }
    }

    /**
     * Prepare related grid data
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_prepareRelatedOrders(
            array(
                'increment_id',
                'created_at',
                'customer_firstname',
                'customer_lastname',
                'base_grand_total',
                'status'
            )
        );
        $this->_relatedOrders->addFieldToFilter('state', array('in' => $this->_config->getVisibleOnFrontStates()));

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager'
        )->setCollection(
            $this->_relatedOrders
        )->setIsOutputRequired(
            false
        );
        $this->setChild('pager', $pager);

        $this->setGridColumns(
            array(
                new \Magento\Object(
                    array('index' => 'increment_id', 'title' => __('Order #'), 'is_nobr' => true, 'width' => 1)
                ),
                new \Magento\Object(
                    array('index' => 'created_at', 'title' => __('Date'), 'is_nobr' => true, 'width' => 1)
                ),
                new \Magento\Object(array('index' => 'customer_name', 'title' => __('Customer Name'))),
                new \Magento\Object(
                    array(
                        'index' => 'base_grand_total',
                        'title' => __('Order Total'),
                        'is_nobr' => true,
                        'width' => 1,
                        'is_amount' => true
                    )
                ),
                new \Magento\Object(
                    array('index' => 'status', 'title' => __('Order Status'), 'is_nobr' => true, 'width' => 1)
                )
            )
        );

        $orders = array();
        foreach ($this->_relatedOrders as $order) {
            $orders[] = new \Magento\Object(
                array(
                    'increment_id' => $order->getIncrementId(),
                    'created_at' => $this->formatDate($order->getCreatedAt()),
                    'customer_name' => $order->getCustomerName(),
                    'base_grand_total' => $this->_coreHelper->formatCurrency($order->getBaseGrandTotal(), false),
                    'status' => $order->getStatusLabel(),
                    'increment_id_link_url' => $this->getUrl('sales/order/view/', array('order_id' => $order->getId()))
                )
            );
        }
        if ($orders) {
            $this->setGridElements($orders);
        }
    }
}
