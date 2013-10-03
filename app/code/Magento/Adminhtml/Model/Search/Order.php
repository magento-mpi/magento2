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
 * Search Order Model
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Model\Search;

class Order extends \Magento\Object
{
    /**
     * Adminhtml data
     *
     * @var \Magento\Adminhtml\Helper\Data
     */
    protected $_adminhtmlData = null;

    /**
     * @var \Magento\Sales\Model\Resource\Order\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Sales\Model\Resource\Order\CollectionFactory $collectionFactory
     * @param \Magento\Adminhtml\Helper\Data $adminhtmlData
     */
    public function __construct(
        \Magento\Sales\Model\Resource\Order\CollectionFactory $collectionFactory,
        \Magento\Adminhtml\Helper\Data $adminhtmlData
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_adminhtmlData = $adminhtmlData;
    }

    /**
     * Load search results
     *
     * @return \Magento\Adminhtml\Model\Search\Order
     */
    public function load()
    {
        $result = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);
            return $this;
        }

        $query = $this->getQuery();
        //TODO: add full name logic
        $collection = $this->_collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToSearchFilter(array(
                array('attribute' => 'increment_id',       'like'=>$query.'%'),
                array('attribute' => 'billing_firstname',  'like'=>$query.'%'),
                array('attribute' => 'billing_lastname',   'like'=>$query.'%'),
                array('attribute' => 'billing_telephone',  'like'=>$query.'%'),
                array('attribute' => 'billing_postcode',   'like'=>$query.'%'),

                array('attribute' => 'shipping_firstname', 'like'=>$query.'%'),
                array('attribute' => 'shipping_lastname',  'like'=>$query.'%'),
                array('attribute' => 'shipping_telephone', 'like'=>$query.'%'),
                array('attribute' => 'shipping_postcode',  'like'=>$query.'%'),
            ))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $order) {
            $result[] = array(
                'id'                => 'order/1/'.$order->getId(),
                'type'              => __('Order'),
                'name'              => __('Order #%1', $order->getIncrementId()),
                'description'       => $order->getBillingFirstname().' '.$order->getBillingLastname(),
                'form_panel_title'  => __('Order #%1 (%2)',
                    $order->getIncrementId(),
                    $order->getBillingFirstname() . ' ' . $order->getBillingLastname()),
                'url' => $this->_adminhtmlData->getUrl('*/sales_order/view', array('order_id' => $order->getId())),
            );
        }

        $this->setResults($result);

        return $this;
    }
}
