<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block;

use Magento\Framework\View\Element\Template;

class Recommendations extends Template
{

    /**
     * @var \Magento\Search\Model\RecommendationsInterface
     */
    private $recommendations;

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    private $catalogSearchData;

    /**
     * @param Template\Context $context
     * @param array $data
     * @param \Magento\Search\Model\RecommendationsInterface $recommendations
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     */
    public function __construct(
        Template\Context $context,
        \Magento\Search\Model\RecommendationsInterface $recommendations,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        array $data = array()
    ) {
        $this->recommendations = $recommendations;
        $this->catalogSearchData = $catalogSearchData;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve search suggestions
     *
     * @return array
     */
    public function getRecommendations()
    {
        $searchQueryText = $this->catalogSearchData->getQuery()->getQueryText();
        return $this->recommendations->getRecommendations($searchQueryText);
    }

    /**
     * @return bool
     */
    public function isCountResultsEnabled()
    {
        return $this->recommendations->isCountResultsEnabled();
    }

    /**
     * @param string $queryText
     * @return string
     */
    public function getLink($queryText)
    {
        return $this->getUrl('*/*/') . '?q=' . urlencode($queryText);
    }
}
