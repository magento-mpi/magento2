<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Multishipping\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * class MultishippingCheckoutSuccess
 * Multishipping checkout success page
 *
 */
class MultishippingCheckoutSuccess extends Page
{
    /**
     * URL for multishipping success page
     */
    const MCA = 'multishipping/checkout/success';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get success block
     *
     * @return \Magento\Multishipping\Test\Block\Checkout\Success
     */
    public function getSuccessBlock()
    {
        return Factory::getBlockFactory()->getMagentoMultishippingCheckoutSuccess(
            $this->_browser->find('.multicheckout.success', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get page title block
     *
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return Factory::getBlockFactory()->getMagentoThemeHtmlTitle(
            $this->_browser->find('.page.title', Locator::SELECTOR_CSS)
        );
    }
}
