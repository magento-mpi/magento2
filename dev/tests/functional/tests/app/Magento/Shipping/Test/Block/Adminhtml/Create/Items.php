<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Adminhtml\Create;

use Mtf\Block\Block;
use Magento\Shipping\Test\Block\Adminhtml\Create\Items\Product;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Items
 * Adminhtml items to ship block
 */
class Items extends Block
{
    /**
     * Shipment submit button
     *
     * @var string
     */
    protected $submitShipment = '[data-ui-id="order-items-submit-button"]';

    /**
     * Shipment comment css selector
     *
     * @var string
     */
    protected $comment = '[name="shipment[comment_text]"]';

    /**
     * Item product
     *
     * @var string
     */
    protected $productItems = '//tr[contains(.,"%s")]';

    /**
     * Get item product block
     *
     * @param FixtureInterface $product
     * @return Product
     */
    public function getItemProductBlock(FixtureInterface $product)
    {
        $selector = sprintf($this->productItems, $product->getSku());
        return $this->blockFactory->create(
            'Magento\Shipping\Test\Block\Adminhtml\Create\Items\Product',
            ['element' => $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)]
        );
    }
}
