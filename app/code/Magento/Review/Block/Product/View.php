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
namespace Magento\Review\Block\Product;

class View extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var \Magento\Review\Model\Resource\Review\Collection
     */
    protected $_reviewsCollection;

    /**
     * @var \Magento\Review\Model\Resource\Review\CollectionFactory
     */
    protected $_reviewsColFactory;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Stdlib\String $string
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Review\Model\Resource\Review\CollectionFactory $collectionFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Stdlib\String $string,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Math\Random $mathRandom,
        \Magento\Review\Model\Resource\Review\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_reviewsColFactory = $collectionFactory;
        parent::__construct(
            $storeManager,
            $catalogConfig,
            $productFactory,
            $locale,
            $taxCalculation,
            $coreRegistry,
            $string,
            $taxData,
            $catalogData,
            $coreData,
            $context,
            $mathRandom,
            $data
        );
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
     * @param \Magento\Catalog\Model\Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(\Magento\Catalog\Model\Product $product, $templateType = false, $displayIfNoReviews = false)
    {
        return
            $this->getLayout()->createBlock('Magento\Rating\Block\Entity\Detailed')
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
                ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
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
