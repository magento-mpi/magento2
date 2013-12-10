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
 * Class SalesGuestForm
 * Orders and returns search page
 *
 * @package Magento\Sales\Test\Page
 */
class SalesGuestForm extends Page
{
    /**
     * URL for orders and returns search page
     */
    const MCA = 'sales/guest/form';

    /**
     * Form wrapper selector
     *
     * @var string
     */
    protected $formWrapperSelector = 'oar-widget-orders-and-returns-form';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get search block form
     *
     * @return \Magento\Sales\Test\Block\Widget\Guest\Form
     */
    public function getSearchForm()
    {
        return Factory::getBlockFactory()->getMagentoSalesWidgetGuestForm(
            $this->_browser->find($this->formWrapperSelector, Locator::SELECTOR_ID)
        );
    }
}
