<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Product;

use Magento\Review\Model\Resource\Review\Collection as ReviewCollection;
use Magento\Tax\Service\V1\TaxCalculationServiceInterface;

/**
 * Product Reviews Page
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class View extends \Magento\Catalog\Block\Product\View
{
    /**
     * Review collection
     *
     * @var ReviewCollection
     */
    protected $_reviewsCollection;

    /**
     * Review resource model
     *
     * @var \Magento\Review\Model\Resource\Review\CollectionFactory
     */
    protected $_reviewsColFactory;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param TaxCalculationServiceInterface $taxCalculationService
     * @param \Magento\Review\Model\Resource\Review\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Framework\Stdlib\String $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        TaxCalculationServiceInterface $taxCalculationService,
        \Magento\Review\Model\Resource\Review\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_reviewsColFactory = $collectionFactory;
        parent::__construct(
            $context,
            $coreData,
            $jsonEncoder,
            $productFactory,
            $taxCalculation,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $taxCalculationService,
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
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        return $this->getLayout()->createBlock(
            'Magento\Review\Block\Rating\Entity\Detailed'
        )->setEntityId(
            $this->getProduct()->getId()
        )->toHtml() . $this->getLayout()->getBlock(
            'product_review_list.count'
        )->assign(
            'count',
            $this->getReviewsCollection()->getSize()
        )->toHtml();
    }

    /**
     * Get collection of reviews
     *
     * @return ReviewCollection
     */
    public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $this->getProduct()->getId()
            )->setDateOrder();
        }
        return $this->_reviewsCollection;
    }

    /**
     * Force product view page behave like without options
     *
     * @return bool
     */
    public function hasOptions()
    {
        return false;
    }
}
