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

use Mtf\Fixture\DataFixture;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class ReturnItem
 * Return Item page
 *
 * @package Magento\Rma\Test\Page
 */
class ReturnItem extends Page
{
    /**
     * URL for return page
     */
    const MCA = 'rma/guest/create';

    /**
     * Form wrapper selector
     *
     * @var string
     */
    private $formWrapperSelector = '.form.create.return';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA; //. '/order_id/' . $orderId;
    }

    /**
     * Get search block form
     *
     * @return \Magento\Rma\Test\Block\Form\ReturnItem
     */
    public function getReturnItemForm()
    {
        return Factory::getBlockFactory()->getMagentoRmaFormReturnItem(
            $this->_browser->find($this->formWrapperSelector, Locator::SELECTOR_CSS)
        );
    }
}
