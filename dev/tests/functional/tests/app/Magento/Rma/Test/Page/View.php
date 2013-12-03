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

namespace Magento\Rma\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class View
 * View orders page
 *
 * @package Magento\Rma\Test\Page
 */
class View extends Page
{
    /**
     * URL for order view page
     */
    const MCA = 'sales/guest/view';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get View block
     *
     * @return \Magento\Rma\Test\Block\View
     */
    public function getViewBlock()
    {
        return Factory::getBlockFactory()->getMagentoRmaView(
            $this->_browser->find('//div[@class="order toolbar"]', Locator::SELECTOR_XPATH)
        );
    }
}