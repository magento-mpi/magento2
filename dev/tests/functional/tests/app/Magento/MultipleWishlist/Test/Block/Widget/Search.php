<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\Block\Widget;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Search
 * Wish list search block
 */
class Search extends Block
{
    /**
     * Search button button css selector
     *
     * @var string
     */
    protected $searchButton = './/form[not(@style="display:none;")]//*[@type="submit"]';

    /**
     * Input field by customer email param
     *
     * @var string
     */
    protected $emailInput = '[name="params[email]"]';

    /**
     * Search type selector
     *
     * @var string
     */
    protected $searchType = '[name="search_by"]';

    /**
     * Search wish list by customer email
     *
     * @param string $email
     * @return void
     */
    public function searchByEmail($email)
    {
        if ($this->_rootElement->find($this->searchType, Locator::SELECTOR_CSS, 'select')->isVisible()) {
            $this->selectSearchType('Wish List Owner Email Search');
        }
        $this->_rootElement->find($this->emailInput)->setValue($email);
        $this->clickSearchButton();
    }

    /**
     * Select search type
     *
     * @param string $type
     * @return void
     */
    protected function selectSearchType($type)
    {
        $this->_rootElement->find($this->searchType, Locator::SELECTOR_CSS, 'select')->setValue($type);
    }

    /**
     * Click button search
     *
     * @return void
     */
    public function clickSearchButton()
    {
        $this->_rootElement->find($this->searchButton, Locator::SELECTOR_XPATH)->click();
    }
}
