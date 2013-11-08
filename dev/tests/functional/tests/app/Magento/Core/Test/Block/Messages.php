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
 * Class Messages
 * Messages block
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
     * Check for success message
     *
     * @return bool
     */
    public function waitForSuccessMessage()
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
}
