<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Element\Locator;
use Mtf\Client\Driver\Selenium\Element;

/**
 * Class SuggestElement
 * General class for suggest elements.
 */
class SuggestElement extends Element
{
    /**
     * Selector suggest input
     *
     * @var string
     */
    protected $suggest = '.mage-suggest-inner > .search';

    /**
     * Selector search result
     *
     * @var string
     */
    protected $searchResult = '.mage-suggest-dropdown';

    /**
     * Selector item of search result
     *
     * @var string
     */
    protected $resultItem = './/*[contains(@class,"mage-suggest-dropdown")]/ul/li/a[text()="%s"]';

    /**
     * Set value
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->_eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);

        $this->find($this->suggest)->setValue($value);
        $this->waitResult();
        $this->find(sprintf($this->resultItem, $value), Locator::SELECTOR_XPATH)->click();
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
     * @return string
     */
    public function getValue()
    {
        $this->_eventManager->dispatchEvent(['get_value'], [(string) $this->_locator]);
        
        return $this->find($this->suggest)->getValue();
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

        $this->find($this->suggest)->setValue($value);
        $this->waitResult();
        if (!$searchResult->isVisible()) {
            return false;
        }
        return $searchResult->find(sprintf($this->resultItem, $value), Locator::SELECTOR_XPATH)->isVisible();
    }
}
