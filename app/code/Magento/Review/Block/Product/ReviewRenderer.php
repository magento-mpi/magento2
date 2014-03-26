<?php
/**
 * Review renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Product;

use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Model\Product;

class ReviewRenderer extends \Magento\View\Element\Template implements ReviewRendererInterface
{
    /**
     * Array of available template name
     *
     * @var array
     */
    protected $_availableTemplates = array(
        self::DEFAULT_REVIEW => 'helper/summary.phtml',
        self::SHORT_REVIEW => 'helper/summary_short.phtml'
    );

    /**
     * Review model factory
     *
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        array $data = array()
    ) {
        $this->_reviewFactory = $reviewFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get review summary html
     *
     * @param Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     *
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = self::DEFAULT_REVIEW,
        $displayIfNoReviews = false
    ) {

        //moved from doubled method calls ($product->getRatingSummary() and $this->getReviewsSummaryHtml())
        //covered not all cases
        // 1. app/code/Magento/Catalog/view/frontend/product/compare/list.phtml
        // 2. app/code/Magento/Review/view/frontend/view.phtml
        // 3. /app/code/Magento/Review/view/frontend/customer/view.phtml

        if (!$product->getRatingSummary() && !$displayIfNoReviews) {
            return '';
        }
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = self::DEFAULT_REVIEW;
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        if (!$product->getRatingSummary()) {
            $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        }
        $this->setProduct($product);

        return $this->toHtml();
    }

    /**
     * Get ratings summary
     *
     * @return string
     */
    public function getRatingSummary()
    {
        return $this->getProduct()->getRatingSummary()->getRatingSummary();
    }

    /**
     * Get count of reviews
     *
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->getProduct()->getRatingSummary()->getReviewsCount();
    }

    /**
     * Get review product list url
     *
     * @return string
     */
    public function getReviewsUrl()
    {
        return $this->getUrl(
            'review/product/list',
            array('id' => $this->getProduct()->getId(), 'category' => $this->getProduct()->getCategoryId())
        );
    }
}
