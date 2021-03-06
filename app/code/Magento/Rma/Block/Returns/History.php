<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Block\Returns;

class History extends \Magento\Framework\View\Element\Template
{
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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Initialize rma history content
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('return/history.phtml');
        /** @var $returns \Magento\Rma\Model\Resource\Rma\Grid\Collection */
        $returns = $this->_collectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'customer_id',
            $this->_customerSession->getCustomer()->getId()
        )->setOrder(
            'date_requested',
            'desc'
        );
        $this->setReturns($returns);
    }

    /**
     * Prepare rma returns history layout
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
     * Get rma pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get rma view url
     *
     * @param \Magento\Framework\Object $return
     * @return string
     */
    public function getViewUrl($return)
    {
        return $this->getUrl('*/*/view', ['entity_id' => $return->getId()]);
    }

    /**
     * Get customer account back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
