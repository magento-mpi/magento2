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

namespace Magento\Multishipping\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * class MultishippingCheckoutOverview
 * Review order page
 *
 * @package Magento\Multishipping\Test\Page
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
