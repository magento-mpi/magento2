<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class PaypalExpressReview.
 * Paypal Express Review page
 *
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
     * @var string
     */
    protected $reviewBlock = '#order-review-form';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get review block
     *
     * @return \Magento\Paypal\Test\Block\Express\Review
     */
    public function getReviewBlock()
    {
        return Factory::getBlockFactory()->getMagentoPaypalExpressReview(
            $this->_browser->find($this->reviewBlock, Locator::SELECTOR_CSS)
        );
    }
}
