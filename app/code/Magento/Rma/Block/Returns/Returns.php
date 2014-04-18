<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Returns;

class Returns extends \Magento\Framework\View\Element\Template
{
    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Rma grid collection
     *
     * @var \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        $this->_coreRegistry = $registry;
        $this->_collectionFactory = $collectionFactory;
        $this->_customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->_isScopePrivate = true;
        parent::__construct($context, $data);
    }

    /**
     * Initialize returns content
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        if ($this->_rmaData->isEnabled()) {
            $this->setTemplate('return/returns.phtml');
            /** @var $returns \Magento\Rma\Model\Resource\Rma\Grid\Collection */
            $returns = $this->_collectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'order_id',
                $this->_coreRegistry->registry('current_order')->getId()
            )->setOrder(
                'date_requested',
                'desc'
            );

            if ($this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)) {
                $returns->addFieldToFilter('customer_id', $this->_customerSession->getCustomer()->getId());
            }
            $this->setReturns($returns);
        }
    }

    /**
     * Prepare rma returns layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'sales.order.history.pager'
        )->setCollection(
            $this->getReturns()
        );
        $this->setChild('pager', $pager);
        $this->getReturns()->load();
        return $this;
    }

    /**
     * Get pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get rma returns view url
     *
     * @param \Magento\Object $return
     * @return string
     */
    public function getViewUrl($return)
    {
        return $this->getUrl('*/*/view', array('entity_id' => $return->getId()));
    }

    /**
     * Get sales order history url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('sales/order/history');
    }

    /**
     * Get sales order reorder url
     *
     * @param \Magento\Object $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', array('order_id' => $order->getId()));
    }

    /**
     * Get sales guest print url
     *
     * @param \Magento\Object $order
     * @return string
     */
    public function getPrintUrl($order)
    {
        return $this->getUrl('sales/guest/print', array('order_id' => $order->getId()));
    }
}
