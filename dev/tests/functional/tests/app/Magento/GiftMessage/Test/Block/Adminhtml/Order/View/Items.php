<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Block\Adminhtml\Order\View;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\InjectableFixture;

/**
 * Class Items
 * Adminhtml GiftMessage order view items block.
 */
class Items extends Block
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
     * @return \Magento\GiftMessage\Test\Block\Adminhtml\Order\View\Items\ItemProduct
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
