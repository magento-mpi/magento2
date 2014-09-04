<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Abstract Class Sidebar
 * Sidebar block
 */
abstract class Sidebar extends Block
{
    /**
     * 'Add to order' checkbox
     *
     * @var string
     */
    protected $addToOrder = '//tr[td[.="%s"]]//input[contains(@name,"add")]';

    /**
     * Add product to order by name
     *
     * @param string|array $names
     * @return void
     */
    public function addToOrderByName($names)
    {
        $names = is_array($names) ? $names : [$names];
        foreach ($names as $name) {
            $this->_rootElement->find(sprintf($this->addToOrder, $name), Locator::SELECTOR_XPATH, 'checkbox')
                ->setValue('Yes');
        }
    }
}
