<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Rating\Entity;

/**
 * Entity rating block
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Detailed extends \Magento\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'detailed.phtml';

    /**
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        array $data = array()
    ) {
        $this->_ratingFactory = $ratingFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $entityId = $this->_request->getParam('id');
        if (intval($entityId) <= 0) {
            return '';
        }

        $reviewsCount = $this->_ratingFactory->create()->getTotalReviews($entityId, true);
        if ($reviewsCount == 0) {
            #return __('Be the first to review this product');
            $this->setTemplate('empty.phtml');
            return parent::_toHtml();
        }

        $ratingCollection = $this->_ratingFactory->create()->getResourceCollection()->addEntityFilter(
            'product' # TOFIX
        )->setPositionOrder()->setStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addRatingPerStoreName(
            $this->_storeManager->getStore()->getId()
        )->load();

        if ($entityId) {
            $ratingCollection->addEntitySummaryToItem($entityId, $this->_storeManager->getStore()->getId());
        }

        $this->assign('collection', $ratingCollection);
        return parent::_toHtml();
    }
}
