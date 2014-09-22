<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class Items
 * Adminhtml sales order create items block
 */
class Items extends Form
{
    /**
     * 'Add Products' button
     *
     * @var string
     */
    protected $addProducts = '//button[span[.="Add Products"]]';

    /**
     * Item product
     *
     * @var string
     */
    protected $itemProduct = '//tr[td//*[normalize-space(text())="%s"]]';

    /**
     * Product locator in Items Order grid
     *
     * @var string
     */
    protected $productInItemsOrder = './/td[contains(.,"%s")]';

    /**
     * Click 'Add Products' button
     *
     * @return void
     */
    public function clickAddProducts()
    {
        $element = $this->_rootElement;
        $selector = $this->addProducts;
        $this->_rootElement->waitUntil(
            function () use ($element, $selector) {
                $addProductsButton = $element->find($selector, Locator::SELECTOR_XPATH);
                return $addProductsButton->isVisible() ? true : null;
            }
        );
        $this->_rootElement->find($this->addProducts, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get item product block
     *
     * @param string $name
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Items\ItemProduct
     */
    public function getItemProductByName($name)
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Create\Items\ItemProduct',
            ['element' => $this->_rootElement->find(sprintf($this->itemProduct, $name), Locator::SELECTOR_XPATH)]
        );
    }
}
