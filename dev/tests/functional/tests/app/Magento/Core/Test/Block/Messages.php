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

/**
 * Class Messages
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
    private $successMessage;

    /**
     * Error message
     *
     * @var string
     */
    private $errorMessage;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->successMessage = '[data-ui-id=messages-message-success]';
        $this->errorMessage = '[data-ui-id=messages-message-error]';
    }

    /**
     * Check for success message
     *
     * @return mixed
     */
    public function assertSuccessMessage()
    {
        return $this->waitForElementVisible($this->successMessage);
    }

    /**
     * Get all success messages which are present on the page
     *
     * @return string
     */
    public function getSuccessMessages()
    {
        return $this->_rootElement
            ->find($this->successMessage)
            ->getText();
    }

    /**
     * Check for error message
     *
     * @return mixed
     */
    public function assertErrorMessage()
    {
        return $this->waitForElementVisible($this->errorMessage);
    }
}
