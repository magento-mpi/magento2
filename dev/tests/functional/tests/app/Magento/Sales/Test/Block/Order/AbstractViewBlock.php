<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class AbstractViewBlock
 * Abstract view block on order's view page
 */
class AbstractViewBlock extends Block
{
    /**
     * Item block
     *
     * @var string
     */
    protected $itemBlock = '//*[@class="order-title" and contains(.,"%d")]';

    /**
     * Content block
     *
     * @var string
     */
    protected $content = '/following-sibling::div[contains(@class,"order-items")][1]';

    /**
     * Get item block
     *
     * @param int $id
     * @return Items
     */
    public function getItemBlock($id)
    {
        $selector = sprintf($this->itemBlock, $id) . $this->content;
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Order\Items',
            ['element' => $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)]
        );
    }
}
