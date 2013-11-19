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

namespace Magento\Backend\Test\Block\CustomerSegment;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Actions
 * Segment actions block
 *
 * @package Magento\Backend\Test\Block\CustomerSegment
 */
class Actions extends Block {
    /**
     * Add condition
     */
    public function clickAddNew()
    {
        $this->_rootElement->find('img.rule-param-add.v-middle')->click();
    }
}