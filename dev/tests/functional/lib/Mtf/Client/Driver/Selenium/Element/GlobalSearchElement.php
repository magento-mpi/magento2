<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Driver\Selenium\Element;

/**
 * Class GlobalSearchElement
 * Typified element class for global search element
 */
class GlobalSearchElement extends Element
{
    /**
     * Selector suggest input
     *
     * @var string
     */
    protected $suggest = '.mage-suggest-inner > [class^="search"]';

    /**
     * Result dropdown selector
     *
     * @var string
     */
    protected $searchResult = '.search-global-menu';

    /**
     * Item selector of search result
     *
     * @var string
     */
    protected $resultItem = 'li';

    /**
     * Search icon selector
     *
     * @var string
     */
    protected $searchIcon = '[for="search-global"]';

    /**
     * Set value
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->_eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);

        $this->find($this->searchIcon)->click();
        $this->find($this->suggest)->setValue($value);
        $this->waitResult();
    }

    /**
     * Wait for search result is visible
     *
     * @return void
     */
    public function waitResult()
    {
        $browser = $this;
        $selector = $this->searchResult;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                return $browser->find($selector)->isVisible() ? true : null;
            }
        );
    }

    /**
     * Get value
     *
     * @throws \BadMethodCallException
     */
    public function getValue()
    {
        throw new \BadMethodCallException('Not applicable for this class of elements (GlobalSearch)');
    }

    /**
     * Checking exist value in search result
     *
     * @param string $value
     * @return bool
     */
    public function isExistValueInSearchResult($value)
    {
        $searchResult = $this->find($this->searchResult);
        if (!$searchResult->isVisible()) {
            return false;
        }
        $searchResults = $this->getSearchResults();
        return in_array($value, $searchResults);
    }

    /**
     * Get search results
     *
     * @return array
     */
    protected function getSearchResults()
    {
        /** @var Element $searchResult */
        $searchResult = $this->find($this->searchResult);
        $resultItems = $searchResult->find($this->resultItem)->getElements();
        $resultArray = [];
        /** @var Element $resultItem */
        foreach ($resultItems as $resultItem) {
            $resultText = explode("\n", $resultItem->getText())[0];
            $resultArray[] = $resultText;
        }
        return $resultArray;
    }
}
