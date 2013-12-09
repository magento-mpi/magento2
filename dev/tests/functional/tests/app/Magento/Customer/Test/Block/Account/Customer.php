<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Account;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Theme\Test\Block\Links;

/**
 * Customer block
 *
 * @package Magento\Customer\Test\Block
 */
class Customer extends Links
{
    /**
     * Toggle customer menu
     */
    public function toggle()
    {
        $this->_rootElement
            ->find('.customer .name', Locator::SELECTOR_CSS)
            ->click();
    }
}
