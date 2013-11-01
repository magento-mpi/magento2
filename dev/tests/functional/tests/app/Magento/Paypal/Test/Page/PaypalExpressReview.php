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
use Mtf\Client\Element\Locator;
use Magento\Paypal\Test\Block\Express;

/**
 * Class PaypalExpressReview.
 * Paypal Express Review page
 *
 * @package Magento\Paypal\Test\Page
 */
class PaypalExpressReview extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'paypal/express/review';

    /**
     * Paypal review block
     *
     * @var Express\Review
     */
    private $reviewBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;

        //Blocks
        $this->reviewBlock = Factory::getBlockFactory()->getMagentoPaypalExpressReview(
            $this->_browser->find('order-review-form', Locator::SELECTOR_ID));
    }

    /**
     * Get review block
     *
     * @return \Magento\Paypal\Test\Block\Express\Review
     */
    public function getReviewBlock()
    {
        return $this->reviewBlock;
    }
}
