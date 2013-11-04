<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Magento\Paypal\Test\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Paypal.
 * Paypal page
 *
 * @package Magento\Paypal\Test\Page
 */
class Paypal extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'paypal';

    /**
     * Form for customer login
     *
     * @var Block\Login
     */
    private $loginBlock;

    /**
     * Paypal review block
     *
     * @var Block\Review
     */
    private $reviewBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = 'https://www.sandbox.paypal.com/cgi-bin/';

        //Blocks
        $this->loginBlock = Factory::getBlockFactory()->getMagentoPaypalLogin(
            $this->_browser->find('loginBox', Locator::SELECTOR_ID));
        $this->reviewBlock = Factory::getBlockFactory()->getMagentoPaypalReview(
            $this->_browser->find('reviewModule', Locator::SELECTOR_ID));
    }

    /**
     * Get login block
     *
     * @return \Magento\Paypal\Test\Block\Login
     */
    public function getLoginBlock()
    {
        return $this->loginBlock;
    }

    /**
     * Get review block
     *
     * @return \Magento\Paypal\Test\Block\Review
     */
    public function getReviewBlock()
    {
        return $this->reviewBlock;
    }
}
