<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Backend\Order;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

Class OrderViewTabs extends Block
{
    /**
     * Click "Ship" button
     */
    public function clickReturnsLink()
    {
        $this->_rootElement->find('[data-ui-id=sales-order-tabs-tab-link-order-rma]',
            Locator::SELECTOR_CSS)->click();
    }
}