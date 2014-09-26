<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block;

use Magento\Framework\View\Element\Template;

class Suggestions extends Template
{

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    private $catalogSearchData;

    /**
     * @param Template\Context $context
     * @param array $data
     * @param \Magento\Search\Model\SuggestionsInterface $suggestions
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     */
    public function __construct(
        Template\Context $context,
        \Magento\Search\Model\SuggestionsInterface $suggestions,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        array $data = array()
    ) {
        $this->suggestions = $suggestions;
        $this->catalogSearchData = $catalogSearchData;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Search\Model\QueryResult[]
     */
    public function getSuggestions()
    {
        $searchQueryText = $this->catalogSearchData->getQuery()->getQueryText();
        return $this->suggestions->getSuggestions($searchQueryText);
    }

    /**
     * @param string $queryText
     * @return string
     */
    public function getLink($queryText)
    {
        return $this->getUrl('*/*/') . '?q=' . urlencode($queryText);
    }

    /**
     * @return bool
     */
    public function isCountResultsEnabled()
    {
        return $this->suggestions->isCountResultsEnabled();
    }
}
