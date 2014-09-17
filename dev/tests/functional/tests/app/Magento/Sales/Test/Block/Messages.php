<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block;

/**
 * Class Messages
 * Warning message block
 */
class Messages extends \Magento\Core\Test\Block\Messages
{
    /**
     * Warning message selector.
     *
     * @var string
     */
    protected $warningMessage = '[data-ui-id$=message-warning]';

    /**
     * Get warning message which is present on the page
     *
     * @return string
     */
    public function getWarningMessages()
    {
        $this->waitForElementVisible($this->warningMessage);
        return $this->_rootElement->find($this->warningMessage)->getText();
    }
}
