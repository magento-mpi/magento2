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

namespace Magento\Checkout\Test\Block;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Block\Onepage;
use Magento\Checkout\Test\Block\Multishipping;

/**
 * Class Cart
 * Shopping cart block
 *
 * @package Magento\Checkout\Test\Block
 */
class Cart extends Block
{
    /**
     * @var Onepage\Link;
     */
    private $onepageLinkBlock;
    /**
     * @var Multishipping\Link;
     */
    private $multishippingLinkBlock;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->onepageLinkBlock = Factory::getBlockFactory()->getMagentoCheckoutOnepageLink(
            $this->_rootElement->find('//button[contains(@class, "checkout")]', Locator::SELECTOR_XPATH));
        $this->multishippingLinkBlock = Factory::getBlockFactory()->getMagentoCheckoutMultishippingLink(
            $this->_rootElement->find('[title="Checkout with Multiple Addresses"]'));
    }

    /**
     * Get proceed to checkout block
     *
     * @return Onepage\Link
     */
    public function getOnepageLinkBlock()
    {
        return $this->onepageLinkBlock;
    }

    /**
     * @return Multishipping\Link
     */
    public function getMultishippingLinkBlock()
    {
        return $this->multishippingLinkBlock;
    }

    /**
     * Check for success message
     *
     * @return bool
     */
    public function waitForProductAdded()
    {
        $this->waitForElementVisible('//span[@data-ui-id="messages-message-success"]', Locator::SELECTOR_XPATH);
    }
}
