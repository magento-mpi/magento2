<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity rating block
 *
 * @category   Magento
 * @package    Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rating\Block\Entity;

class Detailed extends \Magento\View\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'detailed.phtml';

    /**
     * @var \Magento\Rating\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Rating\Model\RatingFactory $ratingFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Rating\Model\RatingFactory $ratingFactory,
        array $data = array()
    ) {
        $this->_ratingFactory = $ratingFactory;
        parent::__construct($context, $coreData, $data);
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

        $reviewsCount = $this->_ratingFactory->create()
            ->getTotalReviews($entityId, true);
        if ($reviewsCount == 0) {
            #return __('Be the first to review this product');
            $this->setTemplate('empty.phtml');
            return parent::_toHtml();
        }

        $ratingCollection = $this->_ratingFactory->create()
            ->getResourceCollection()
            ->addEntityFilter('product') # TOFIX
            ->setPositionOrder()
            ->setStoreFilter($this->_storeManager->getStore()->getId())
            ->addRatingPerStoreName($this->_storeManager->getStore()->getId())
            ->load();

        if ($entityId) {
            $ratingCollection->addEntitySummaryToItem($entityId, $this->_storeManager->getStore()->getId());
        }

        $this->assign('collection', $ratingCollection);
        return parent::_toHtml();
    }
}
