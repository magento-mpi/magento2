<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Product\View;

/**
 * Detailed Product Reviews
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ListView extends \Magento\Review\Block\Product\View
{
    /**
     * @var false
     */
    protected $_forceHasOptions = false;

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->_coreRegistry->registry('product')->getId();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($toolbar = $this->getLayout()->getBlock('product_review_list.toolbar')) {
            $toolbar->setCollection($this->getReviewsCollection());
            $this->setChild('toolbar', $toolbar);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->getReviewsCollection()
            ->load()
            ->addRateVotes();
        return parent::_beforeToHtml();
    }

    /**
     * @return string
     */
    public function getReviewUrl($id)
    {
        return $this->getUrl('*/*/view', array('id' => $id));
    }
}
