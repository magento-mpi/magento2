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

namespace Magento\CustomerSegment\Test\Block\Backend\Adminhtml;

use Mtf\Block\Block;

/**
 * Class Customersegment
 * Customer Segment actions block
 *
 * @package Magento\CustomerSegment\Test\Block\Backend\Adminhtml
 */
class Customersegment extends Block
{
    /**
     * Click save and continue button on form
     */
    public function clickSaveAndContinue()
    {
        $this->_rootElement->find('#save_and_continue_edit')->click();
    }
}
