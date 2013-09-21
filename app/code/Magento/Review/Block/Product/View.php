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
 * Product Reviews Page
 *
 * @category   Magento
 * @package    Magento_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Review_Block_Product_View extends Magento_Catalog_Block_Product_View
{
    /**
     * @var Magento_Review_Model_Resource_Review_Collection
     */
    protected $_reviewsCollection;

    /**
     * @var Magento_Review_Model_Resource_Review_CollectionFactory
     */
    protected $_reviewsColFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Review_Model_Resource_Review_CollectionFactory $collectionFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Helper_String $coreString,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Review_Model_Resource_Review_CollectionFactory $collectionFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_reviewsColFactory = $collectionFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($coreRegistry, $coreString, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->getProduct()->setShortDescription(null);

        return parent::_toHtml();
    }

    /**
     * Replace review summary html with more detailed review summary
     * Reviews collection count will be jerked here
     *
     * @param Magento_Catalog_Model_Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(Magento_Catalog_Model_Product $product, $templateType = false, $displayIfNoReviews = false)
    {
        return
            $this->getLayout()->createBlock('Magento_Rating_Block_Entity_Detailed')
                ->setEntityId($this->getProduct()->getId())
                ->toHtml()
            .
            $this->getLayout()->getBlock('product_review_list.count')
                ->assign('count', $this->getReviewsCollection()->getSize())
                ->toHtml()
            ;
    }

    public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsColFactory->create()
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addStatusFilter(Magento_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('product', $this->getProduct()->getId())
                ->setDateOrder();
        }
        return $this->_reviewsCollection;
    }

    /**
     * Force product view page behave like without options
     *
     * @return false
     */
    public function hasOptions()
    {
        return false;
    }
}
