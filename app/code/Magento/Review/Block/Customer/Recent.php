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

/**
 * Recent Customer Reviews Block
 */
class Recent extends \Magento\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'customer/list.phtml';

    /**
     * Product reviews collection
     *
     * @var \Magento\Review\Model\Resource\Review\Product\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Review\Model\Resource\Review\Product\CollectionFactory
     */
    protected $_collectionFactory;

    /**
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

    public function count()
    {
        return $this->_getCollection()->getSize();
    }

    protected function _getCollection()
    {
        if (!$this->_collection) {
            $this->_initCollection();
        }
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_getCollection();
    }

    public function getReviewLink()
    {
        return $this->getUrl('review/customer/view/');
    }

    public function getProductLink()
    {
        return $this->getUrl('catalog/product/view/');
    }

    public function dateFormat($date)
    {
        return $this->formatDate($date, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
    }

    public function getAllReviewsUrl()
    {
        return $this->getUrl('review/customer');
    }

    public function getReviewUrl($id)
    {
        return $this->getUrl('review/customer/view', array('id' => $id));
    }
}
