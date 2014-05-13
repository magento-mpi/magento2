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
 * Adminhtml sales order create items block
 *
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
     * Click 'Add Products' button
     */
    public function clickAddProducts()
    {
        $this->_rootElement->find($this->addProducts, Locator::SELECTOR_XPATH)->click();
    }
}
