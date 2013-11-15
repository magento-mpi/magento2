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

namespace Magento\Sales\Test\Block\Backend\Order;

use Magento\Backend\Test\Block\Template;
use Magento\Sales\Test\Fixture\Order;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Block for selection store view for creating order
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class SelectStoreView extends Block
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
     * Select store view for order based on Order fixture
     *
     * @param Order $fixture
     */
    public function selectStoreView(Order $fixture)
    {
        if ($this->isVisible()) {
            $selector = '//label[text()="' . $fixture->getStoreViewName() . '"]/preceding-sibling::*';
            $this->_rootElement->find($selector, Locator::SELECTOR_XPATH, 'checkbox')->setValue('Yes');
            $this->templateBlock->waitLoader();
        }
    }
}
