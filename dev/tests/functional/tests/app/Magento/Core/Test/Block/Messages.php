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
     * Success message
     *
     * @var string
     */
    protected $successMessageSelector = '//*[contains(@data-ui-id, "message-success")]';

    /**
     * Error message
     *
     * @var string
     */
    protected $errorMessageSelector = '//*[contains(@data-ui-id, "message-error")]';

    /**
     * Check for success message
     *
     * @return bool
     */
    public function assertSuccessMessage()
    {
        $this->waitForElementVisible($this->successMessageSelector, Locator::SELECTOR_XPATH);
    }

    /**
     * Get all success messages which are present on the page
     *
     * @return string
     */
    public function getSuccessMessages()
    {
        return $this->_rootElement
            ->find($this->successMessageSelector, Locator::SELECTOR_XPATH)
            ->getText();
    }

    /**
     * Get all success messages which are present on the page
     *
     * @return string
     */
    public function getErrorMessages()
    {
        return $this->_rootElement
            ->find($this->errorMessageSelector, Locator::SELECTOR_XPATH)
            ->getText();
    }

    /**
     * Check for error message
     *
     * @return mixed
     */
    public function assertErrorMessage()
    {
        return $this->waitForElementVisible($this->errorMessageSelector, Locator::SELECTOR_XPATH);
    }
}
