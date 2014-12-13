<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Shipping\Test\Block\Adminhtml\Form;

use Magento\Sales\Test\Block\Adminhtml\Order\AbstractItemsNewBlock;
use Magento\Shipping\Test\Block\Adminhtml\Form\Items\Product;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Items
 * Adminhtml items to ship block
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
            'Magento\Shipping\Test\Block\Adminhtml\Form\Items\Product',
            ['element' => $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)]
        );
    }
}
