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

class CustomerInformation extends Block
{
    public function getCustomerEmail()
    {
        $selector = '//th[text()="Email"]/following-sibling::*/a';
        $email = $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->getText();
        return $email;
    }
} 