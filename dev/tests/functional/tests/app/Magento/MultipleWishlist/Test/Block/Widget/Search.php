<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Block\Widget;

use Mtf\Block\Block;

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
    protected $searchButton = '[type="submit"]';

    /**
     * Input field by customer email param
     *
     * @var string
     */
    protected $emailInput = '[name="params[email]"]';

    /**
     * Search wish list by customer email
     *
     * @param string $email
     */
    public function searchByEmail($email)
    {
        $this->_rootElement->find($this->emailInput)->setValue($email);
        $this->clickButtonSearch();
    }

    /**
     * Click button search
     *
     * @return void
     */
    public function clickButtonSearch()
    {
        $this->_rootElement->find($this->searchButton)->click();
    }
}
