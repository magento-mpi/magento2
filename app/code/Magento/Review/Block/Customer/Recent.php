<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Block\Customer;

use Magento\Review\Model\Resource\Review\Product\Collection;

/**
 * Recent Customer Reviews Block
 */
class Recent extends \Magento\View\Element\Template
{
    /**
     * Customer list template name
     *
     * @var string
     */
    protected $_template = 'customer/list.phtml';

    /**
     * Product reviews collection
     *
     * @var Collection
     */
    protected $_collection;

    /**
     * Review resource model
     *
     * @var \Magento\Review\Model\Resource\Review\Product\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Customer session model
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Review\Model\Resource\Review\Product\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Review\Model\Resource\Review\Product\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filterManager->truncate($value, array(
            'length' => $length,
            'etc' => $etc,
            'remainder' => $remainder,
            'breakWords' => $breakWords
        ));
    }

    /**
     * Initialize review collection
     * @return $this
     */
    protected function _initCollection()
    {
        $this->_collection = $this->_collectionFactory->create();
        $this->_collection
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->addCustomerFilter($this->_customerSession->getCustomerId())
            ->setDateOrder()
            ->setPageSize(5)
            ->load()
            ->addReviewSummary();
        return $this;
    }

    /**
     * Get number of reviews
     *
     * @return int
     */
    public function count()
    {
        return $this->_getCollection()->getSize();
    }

    /**
     * Initialize and return collection of reviews
     * @return Collection
     */
    protected function _getCollection()
    {
        if (!$this->_collection) {
            $this->_initCollection();
        }
        return $this->_collection;
    }

    /**
     * Return collection of reviews
     *
     * @return Collection
     */
    public function getCollection()
    {
        return $this->_getCollection();
    }

    /**
     * Return review customer view url
     *
     * @return string
     */
    public function getReviewLink()
    {
        return $this->getUrl('review/customer/view/');
    }

    /**
     * Return catalog product view url
     *
     * @return string
     */
    public function getProductLink()
    {
        return $this->getUrl('catalog/product/view/');
    }

    /**
     * Format review date
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT);
    }

    /**
     * Return review customer url
     *
     * @return string
     */
    public function getAllReviewsUrl()
    {
        return $this->getUrl('review/customer');
    }

    /**
     * Return review customer view url for a specific customer/review
     *
     * @param int $id
     * @return string
     */
    public function getReviewUrl($id)
    {
        return $this->getUrl('review/customer/view', array('id' => $id));
    }
}
