<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\Block;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Database form.
 */
class Database extends Form
{
    /**
     * 'Test Connection and Authentication' button.
     *
     * @var string
     */
    protected $testConnection = '//*[@class="btn btn-default"]';

    /**
     * 'Test connection successful.' message.
     *
     * @var string
     */
    protected $successConnectionMessage = "//*[@class='animate-show text-success']";

    /**
     * 'Next' button.
     *
     * @var string
     */
    protected $next = "//*[.='Next']";

    /**
     * Click on 'Test Connection and Authentication' button.
     *
     * @return void
     */
    public function clickTestConnection()
    {
        $this->_rootElement->find($this->testConnection, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get 'Test connection successful.' message.
     *
     * @return string
     */
    public function getSuccessConnectionMessage()
    {
        return $this->_rootElement->find($this->successConnectionMessage, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Click on 'Next' button.
     *
     * @return void
     */
    public function clickNext()
    {
        $this->_rootElement->find($this->next, Locator::SELECTOR_XPATH)->click();
    }
}