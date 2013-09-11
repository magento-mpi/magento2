<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Reviews list block
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Review\Block\Customer;

class ListCustomer extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * Product reviews collection
     *
     * @var \Magento\Review\Model\Resource\Review\Product\Collection
     */
    protected $_collection;

    protected function _initCollection()
    {
        $this->_collection = \Mage::getModel('\Magento\Review\Model\Review')->getProductCollection();
        $this->_collection
            ->addStoreFilter(\Mage::app()->getStore()->getId())
            ->addCustomerFilter(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId())
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
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('\Magento\Page\Block\Html\Pager', 'customer_review_list.toolbar')
            ->setCollection($this->getCollection());

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
        return \Mage::getUrl('review/customer/view/');
    }

    /**
     * Get product link
     *
     * @return string
     */
    public function getProductLink()
    {
        return \Mage::getUrl('catalog/product/view/');
    }

    /**
     * Format date in short format
     *
     * @param $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
    }

    /**
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->_getCollection()
            ->load()
            ->addReviewSummary();
        return parent::_beforeToHtml();
    }
}
