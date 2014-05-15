<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Account;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Links
 * Gift card block of customer account page
 */
class Links extends Block
{
    /**
     * XPath locator for account navigation on customer page
     *
     * @var string
     */
    protected $navItem = '//*[contains(@class,"item")]/a[contains(.,"%s")]';

    /**
     * Select link in menu
     *
     * @param string $link
     * @return void
     */
    public function openMenuItem($link)
    {
        $this->_rootElement->find(sprintf($this->navItem, $link), Locator::SELECTOR_XPATH)->click();
    }
} 