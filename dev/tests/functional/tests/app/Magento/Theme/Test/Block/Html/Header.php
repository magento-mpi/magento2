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

namespace Magento\Theme\Test\Block\Html;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Header
 * Click Register button on frontend index page
 *
 * @package Magento\Theme\Test\Block\Html
 */
class Header extends Block
{
    /**
     * 'Register' button
     *
     * @var string
     */
    protected $register = 'ul.header.links > li > a[href*="/customer/account/create"]';

    /**
     * Click Register button
     *
     * @param Customer/CustomerInjectable $fixture
     */
    public function clickRegisterButton()
    {
        $this->_rootElement->find($this->register, Locator::SELECTOR_CSS)->click();
    }
}