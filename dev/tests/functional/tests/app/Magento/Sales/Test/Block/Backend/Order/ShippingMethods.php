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

namespace Magento\Sales\Test\Block\Backend\Order;

use Magento\Backend\Test\Block\Template;
use Magento\Sales\Test\Fixture\Order;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Class Methods
 * Order creation in backend payment methods
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class ShippingMethods extends Block
{
    /**
     * Global page template block
     *
     * @var Template
     */
    protected $templateBlock;

    /**
     * @inheritdoc
     */
    protected function _init()
    {
        parent::_init();
        $this->templateBlock = Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_rootElement->find('./ancestor::body', Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Select payment method
     *
     * @param Order $fixture
     */
    public function selectShippingMethod(Order $fixture)
    {
        $this->_rootElement->find('#order-shipping-method-summary a')->click();
        $shippingMethod = $fixture->getShippingMethod()->getData('fields');
        $selector = '//dt[contains(., "' . $shippingMethod['shipping_service']
            . '")]/following-sibling::*//input';
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        $this->templateBlock->waitLoader();
    }
}
