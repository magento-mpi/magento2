<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Customer;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;

/**
 * Customer Reviews list block
 */
class ListCustomer extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * Product reviews collection
     *
     * @var \Magento\Review\Model\Resource\Review\Product\Collection
     */
    protected $_collection;

    /**
     * Review resource model
     *
     * @var \Magento\Review\Model\Resource\Review\Product\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentService
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param CustomerAddressServiceInterface $addressService
     * @param \Magento\Review\Model\Resource\Review\Product\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerAccountServiceInterface $customerAccountService,
        CustomerAddressServiceInterface $addressService,
        \Magento\Review\Model\Resource\Review\Product\CollectionFactory $collectionFactory,
        \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerAccountService,
            $addressService,
            $data
        );
        $this->_isScopePrivate = true;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Initialize review collection
     *
     * @return $this
     */
    protected function _initCollection()
    {
        $this->_collection = $this->_collectionFactory->create();
        $this->_collection
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->addCustomerFilter($this->currentCustomer->getCustomerId())
            ->setDateOrder();
        return $this;
    }

    /**
     * Gets collection items count
     *
     * @return int
     */
    public function count()
    {
        return $this->_getCollection()->getSize();
    }

    /**
     * Get html code for toolbar
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'customer_review_list.toolbar'
        )->setCollection(
            $this->getCollection()
        );

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    /**
     * Get collection
     *
     * @return \Magento\Review\Model\Resource\Review\Product\Collection
     */
    protected function _getCollection()
    {
        if (!$this->_collection) {
            $this->_initCollection();
        }
        return $this->_collection;
    }

    /**
     * Get collection
     *
     * @return \Magento\Review\Model\Resource\Review\Product\Collection
     */
    public function getCollection()
    {
        return $this->_getCollection();
    }

    /**
     * Get review link
     *
     * @return string
     */
    public function getReviewLink()
    {
        return $this->getUrl('review/customer/view/');
    }

    /**
     * Get product link
     *
     * @return string
     */
    public function getProductLink()
    {
        return $this->getUrl('catalog/product/view/');
    }

    /**
     * Format date in short format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT);
    }

    /**
     * Add review summary
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->_getCollection()->load()->addReviewSummary();
        return parent::_beforeToHtml();
    }
}
