<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Search
 * Block for search field
 *
 * @package Magento\Catalog\Test\Block
 */
class Search extends Block
{
    /**
     * Search field
     *
     * @var string
     */
    private $searchInput;

    /**
     * Search button
     *
     * @var string
     */
    private $searchButton;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->searchInput = '#search';
        $this->searchButton = '[title="Search"]';
    }

    /**
     * Search products by a keyword
     *
     * @param string $keyword
     */
    public function search($keyword)
    {
        $this->_rootElement->find($this->searchInput, Locator::SELECTOR_CSS)->setValue($keyword);
        $this->_rootElement->find($this->searchButton, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Check that placeholder contains text
     *
     * @param string $placeholderText
     * @return bool
     */
    public function isPlaceholderContains($placeholderText)
    {
        $field = $this->_rootElement->find(
            '//input[@id="search" and contains(@placeholder, "' . $placeholderText . '")]', Locator::SELECTOR_XPATH
        );
        return $field->isVisible();
    }
}
