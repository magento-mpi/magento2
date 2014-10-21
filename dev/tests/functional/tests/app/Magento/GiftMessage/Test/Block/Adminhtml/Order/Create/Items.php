<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Block\Adminhtml\Order\Create;

use Mtf\Client\Element\Locator;
use Mtf\Fixture\InjectableFixture;
use Magento\GiftMessage\Test\Block\Adminhtml\Order\Create\Items\ItemProduct;
use Magento\Sales\Test\Block\Adminhtml\Order\Create\Items as ParentItems;

/**
 * Class Items
 * Adminhtml GiftMessage order create items block.
 */
class Items extends ParentItems
{
    /**
     * Item product.
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
            'Magento\GiftMessage\Test\Block\Adminhtml\Order\Create\Items\ItemProduct',
            [
                'element' => $this->browser->find(
                    sprintf($this->itemProduct, $product->getName()),
                    Locator::SELECTOR_XPATH
                )
            ]
        );
    }
}
