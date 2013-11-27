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

namespace Magento\Sales\Test\Block\Adminhtml\Order\View;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Block for information about customer on order page
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order\View
 */
class Info extends Block
{
    /**
     * Customer email
     *
     * @var string
     */
    protected $email = '//th[text()="Email"]/following-sibling::*/a';

    /**
     * Get email from the data inside block
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->_rootElement->find($this->email, Locator::SELECTOR_XPATH)->getText();
    }
}
