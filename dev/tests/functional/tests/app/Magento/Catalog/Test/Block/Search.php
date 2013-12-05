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
    protected $searchInput = '#search';

    /**
     * Search button
     *
     * @var string
     */
    protected $searchButton = '[title="Search"]';

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
}
