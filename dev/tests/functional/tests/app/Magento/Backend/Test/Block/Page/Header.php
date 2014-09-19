<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Page;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Header block
 *
 */
class Header extends Block
{
    /**
     * Selector for Account Avatar
     *
     * @var string
     */
    protected $adminAccountLink = '.admin-user-account';

    /**
     * Selector for Log Out Link
     *
     * @var string
     */
    protected $signOutLink = '.account-signout';

    /**
     * Selector for Search Link
     *
     * @var string
     */
    protected $searchSelector = '#form-search';

    /**
     * Log out Admin User
     */
    public function logOut()
    {
        if ($this->isLoggedIn()) {
            $this->_rootElement->find($this->adminAccountLink)->click();
            $this->_rootElement->find($this->signOutLink)->click();
            $this->waitForElementNotVisible($this->signOutLink);
        }
    }

    /**
     * Get admin account link visibility
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->_rootElement->find($this->adminAccountLink)->isVisible();
    }

    /**
     * Search the query text
     *
     * @param string $query
     * @return void
     */
    public function search($query)
    {
        /** @var \Mtf\Client\Driver\Selenium\Element\GlobalSearchElement $search */
        $search = $this->_rootElement->find($this->searchSelector, Locator::SELECTOR_CSS, 'globalSearch');
        $search->setValue($query);
    }

    /**
     * Is search result is visible in suggestion dropdown
     *
     * @param string $query
     * @return bool
     */
    public function isSearchResultVisible($query)
    {
        /** @var \Mtf\Client\Driver\Selenium\Element\GlobalSearchElement $search */
        $search = $this->_rootElement->find($this->searchSelector, Locator::SELECTOR_CSS, 'globalSearch');
        return $search->isExistValueInSearchResult($query);
    }
}
