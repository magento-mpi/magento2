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
class Magento_Rating_Block_Entity_Detailed extends Magento_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'detailed.phtml';

    /**
     * Store list manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Rating_Model_RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Rating_Model_RatingFactory $ratingFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Rating_Model_RatingFactory $ratingFactory,
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
