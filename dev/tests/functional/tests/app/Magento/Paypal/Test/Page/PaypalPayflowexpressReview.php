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
class PaypalPayflowexpressReview extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'paypal/payflowexpress/review';

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
        return Factory::getBlockFactory()->getMagentoPaypalExpressReviewuk(
            $this->_browser->find($this->reviewBlock, Locator::SELECTOR_CSS)
        );
    }
}
