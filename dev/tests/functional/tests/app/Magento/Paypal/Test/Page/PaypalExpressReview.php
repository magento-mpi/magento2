<?php
/**
 * @spi
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Paypal\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

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
    protected $reviewBlock = '.column.main';

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
