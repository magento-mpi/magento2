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
     * Link for customer login
     *
     * @var string
     */
    protected $billingModuleBlock = '#billingModule';

    /**
     * Form for customer login
     *
     * @var string
     */
    protected $loginBlock = '#loginBox';

    /**
     * Paypal review block
     *
     * @var string
     */
    protected $reviewBlock = '#reviewModule';

    /**
     * Paypal main panel block
     *
     * @var string
     */
    protected $panelMainBlock = '#panelMain';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = 'https://www.sandbox.paypal.com/cgi-bin/';
    }

    /**
     * Get login block
     *
     * @return \Magento\Paypal\Test\Block\Login
     */
    public function getLoginBlock()
    {
        return Factory::getBlockFactory()->getMagentoPaypalLogin(
            $this->_browser->find($this->loginBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get review block
     *
     * @return \Magento\Paypal\Test\Block\Review
     */
    public function getReviewBlock()
    {
        return Factory::getBlockFactory()->getMagentoPaypalReview(
            $this->_browser->find($this->reviewBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get billing module block
     *
     * @return \Magento\Paypal\Test\Block\Billing
     */
    public function getBillingBlock()
    {
        return Factory::getBlockFactory()->getMagentoPaypalBilling(
            $this->_browser->find($this->billingModuleBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get main panel block
     *
     * @return \Magento\Paypal\Test\Block\MainPanel
     */
    public function getMainPanelBlock()
    {
        return Factory::getBlockFactory()->getMagentoPaypalMainPanel(
            $this->_browser->find($this->panelMainBlock, Locator::SELECTOR_CSS)
        );
    }
}
