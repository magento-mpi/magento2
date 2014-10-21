<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Block\Adminhtml\Order\View;

use Mtf\Client\Element\Locator;
use Mtf\Fixture\InjectableFixture;
use Magento\GiftMessage\Test\Block\Adminhtml\Order\View\Items\ItemProduct;
use Magento\Sales\Test\Block\Adminhtml\Order\View\Items as ParentItems;

/**
 * Class Items
 * Adminhtml GiftMessage order view items block.
 */
class Items extends ParentItems
{
    /**
     * Item product selector.
     *
     * @var string
     */
    protected $itemProduct = '//tbody[*[td//*[normalize-space(text())="%s"]]]';

    /**
     * Get item product block.
     *
     * @param InjectableFixture $product
     * @return ItemProduct
     */
    public function getItemProduct(InjectableFixture $product)
    {
        return $this->blockFactory->create(
            'Magento\GiftMessage\Test\Block\Adminhtml\Order\View\Items\ItemProduct',
            [
                'element' => $this->_rootElement->find(
                    sprintf($this->itemProduct, $product->getName()),
                    Locator::SELECTOR_XPATH
                )
            ]
        );
    }
}
