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

namespace Magento\Sales\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class View
 * View orders page
 *
 * @package Magento\Sales\Test\Page
 */
class SalesGuestView extends Page
{
    /**
     * URL for order view page
     */
    const MCA = 'sales/guest/view';

    /**
     * Form wrapper selector
     *
     * @var string
     */
    protected $blockSelector = '//div[@class="order details view"]';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get view block
     *
     * @return \Magento\Sales\Test\Block\Order\Info\Buttons
     */
    public function getViewBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesOrderInfoButtons(
            $this->_browser->find($this->blockSelector, Locator::SELECTOR_XPATH)
        );
    }
}
