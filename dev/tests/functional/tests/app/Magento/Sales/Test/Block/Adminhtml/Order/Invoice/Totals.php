<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Invoice;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Totals
 * Invoice totals block
 */
class Totals extends Block
{
    /**
     * Submit invoice button selector
     *
     * @var string
     */
    protected $submit = '.submit-button';

    /**
     * Capture amount select selector
     *
     * @var string
     */
    protected $capture = '[name="invoice[capture_case]"]';

    /**
     * Submit invoice
     *
     * @return void
     */
    public function submit()
    {
        $this->waitForElementNotVisible($this->submit . '.disabled');
        $this->reinitRootElement();
        $this->_rootElement->find($this->submit)->click();
    }

    /**
     * Set Capture amount option:
     * Capture Online|Capture Offline|Not Capture
     *
     * @param string $option
     * @return void
     */
    public function setCaptureOption($option)
    {
        $this->_rootElement->find($this->capture, Locator::SELECTOR_CSS, 'select')->setValue($option);
    }
}
