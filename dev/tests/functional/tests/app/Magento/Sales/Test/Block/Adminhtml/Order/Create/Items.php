<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Items
 * Adminhtml sales order create items block
 */
class Items extends Block
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
     * Click 'Add Products' button
     *
     * @return void
     */
    public function clickAddProducts()
    {
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
