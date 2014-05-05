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

namespace Magento\Core\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Global messages block
 *
 * @package Magento\Core\Test\Block
 */
class Messages extends Block
{
    /**
     * Success message selector.
     *
     * @var string
     */
    protected $successMessage = '[data-ui-id$=message-success]';

    /**
     * Error message selector.
     *
     * @var string
     */
    protected $errorMessage = '[data-ui-id$=message-error]';

    /**
     * Notice message selector
     *
     * @var string
     */
    protected $noticeMessage = '[data-ui-id$=message-notice]';

    /**
     * Check for success message
     *
     * @return bool
     */
    public function assertSuccessMessage()
    {
        return $this->waitForElementVisible($this->successMessage, Locator::SELECTOR_CSS);
    }

    /**
     * Get all success messages which are present on the page
     *
     * @return string
     */
    public function getSuccessMessages()
    {
        $this->waitForElementVisible($this->successMessage);
        return $this->_rootElement->find($this->successMessage)->getText();
    }

    /**
     * Get all error messages which are present on the page
     *
     * @return string
     */
    public function getErrorMessages()
    {
        return $this->_rootElement
            ->find($this->errorMessage, Locator::SELECTOR_CSS)
            ->getText();
    }

    /**
     * Click on part messages which are present on the page
     *
     * @param $messageType
     * @param $text
     * @internal param $selector
     * @return string
     */
    public function clickLinkInMessages($messageType, $text)
    {
        if ($this->isVisibleMessage('error')) {
            return $this->_rootElement
                ->find($this->{$messageType . 'Message'}, Locator::SELECTOR_CSS)
                ->find("//a[contains(.,'$text')]", Locator::SELECTOR_XPATH)
                ->click();
        }
        return false;

    }

    /**
     * Check is visible error messages
     *
     * @param string $messageType
     * @return string
     */
    public function isVisibleMessage($messageType)
    {
        return $this->_rootElement
            ->find($this->{$messageType . 'Message'}, Locator::SELECTOR_CSS)
            ->isVisible();
    }

    /**
     * Check for error message
     *
     * @return bool
     */
    public function assertErrorMessage()
    {
        return $this->waitForElementVisible($this->errorMessage, Locator::SELECTOR_CSS);
    }

    /**
     * Check for notice message
     *
     * @return bool
     */
    public function assertNoticeMessage()
    {
        return $this->waitForElementVisible($this->noticeMessage, Locator::SELECTOR_CSS);
    }
}
