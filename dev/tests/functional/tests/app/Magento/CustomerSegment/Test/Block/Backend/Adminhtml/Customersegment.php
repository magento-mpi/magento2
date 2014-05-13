<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Backend\Adminhtml;

use Mtf\Block\Block;

/**
 * Class Customersegment
 * Customer Segment actions block
 *
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
