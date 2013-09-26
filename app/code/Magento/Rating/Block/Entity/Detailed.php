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

class Detailed extends \Magento\Core\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'detailed.phtml';

    /**
     * Store list manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Rating\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Rating\Model\RatingFactory $ratingFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Rating\Model\RatingFactory $ratingFactory,
        array $data = array()
    ){
        $this->_storeManager = $storeManager;
        $this->_ratingFactory = $ratingFactory;
        parent::__construct($coreData, $context, $data);
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
