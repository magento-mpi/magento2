<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Form;

use Mtf\Client\Element\Locator;
use Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Form\Items\Product;
use Mtf\Fixture\FixtureInterface;
use Magento\Sales\Test\Block\Adminhtml\Order\AbstractItemsNewBlock;

/**
 * Class Items
 * Block for items to invoice on new invoice page
 */
class Items extends AbstractItemsNewBlock
{
    /**
     * Get item product block
     *
     * @param FixtureInterface $product
     * @return Product
     */
    public function getItemProductBlock(FixtureInterface $product)
    {
        $selector = sprintf($this->productItem, $product->getSku());
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Form\Items\Product',
            ['element' => $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)]
        );
    }
}
