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
use Mtf\Fixture\DataFixture;

/**
 * Class Messages
 * Messages block
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
    private $successMessageSelector;

    /**
     * Error message selector.
     *
     * @var string
     */
    private $errorMessageSelector;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->successMessageSelector = '[data-ui-id=messages-message-success]';
        $this->errorMessageSelector = '[data-ui-id=messages-message-error]';
    }

    /**
     * Check for success message
     *
     * @return bool
     */
    public function waitForSuccessMessage()
    {
        return $this->waitForElementVisible($this->successMessageSelector);
    }

    /**
     * Check for error message
     *
     * @return bool
     */
    public function waitForErrorMessage()
    {
        return $this->waitForElementVisible($this->errorMessageSelector);
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
}
