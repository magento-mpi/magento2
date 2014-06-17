<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Client\Driver\Selenium\Element as AbstractElement;

/**
 * Class SuggestElement
 * General class for suggest elements.
 */
class SuggestElement extends AbstractElement
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
    protected $searchResult = '.mage-suggest-dropdown > ul';

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
     * @return null
     */
    public function getValue()
    {
        return null;
    }
}
