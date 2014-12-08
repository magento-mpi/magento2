<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * class MultishippingCheckoutOverview
 * Review order page
 *
 */
class MultishippingCheckoutOverview extends Page
{
    /**
     * URL for order overview page
     */
    const MCA = 'multishipping/checkout/overview';

    /**
     * Multishipping checkout overview block
     *
     * @var string
     */
    protected $overviewBlock = '#review-order-form';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get overview block
     *
     * @return \Magento\Multishipping\Test\Block\Checkout\Overview
     */
    public function getOverviewBlock()
    {
        return Factory::getBlockFactory()->getMagentoMultishippingCheckoutOverview(
            $this->_browser->find($this->overviewBlock, Locator::SELECTOR_CSS)
        );
    }
}
