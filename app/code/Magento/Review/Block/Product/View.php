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
     * Construct
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Tax_Model_Calculation $taxCalculation
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Review_Model_Resource_Review_CollectionFactory $collectionFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Tax_Model_Calculation $taxCalculation,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Helper_String $coreString,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Review_Model_Resource_Review_CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_reviewsColFactory = $collectionFactory;
        parent::__construct($storeManager, $catalogConfig, $productFactory, $locale, $taxCalculation, $coreRegistry,
            $coreString, $taxData, $catalogData, $coreData, $context, $data);
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
            $this->_reviewsCollection = $this->_reviewsColFactory->create()
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
