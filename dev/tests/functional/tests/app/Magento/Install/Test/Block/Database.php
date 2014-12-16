<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
    protected $testConnection = '.btn-default';

    /**
     * 'Test connection successful.' message.
     *
     * @var string
     */
    protected $successConnectionMessage = ".text-success";

    /**
     * 'Next' button.
     *
     * @var string
     */
    protected $next = "[ng-click*='next']";

    /**
     * Click on 'Test Connection and Authentication' button.
     *
     * @return void
     */
    public function clickTestConnection()
    {
        $this->_rootElement->find($this->testConnection, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Get 'Test connection successful.' message.
     *
     * @return string
     */
    public function getSuccessConnectionMessage()
    {
        return $this->_rootElement->find($this->successConnectionMessage, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Click on 'Next' button.
     *
     * @return void
     */
    public function clickNext()
    {
        $this->_rootElement->find($this->next, Locator::SELECTOR_CSS)->click();
    }
}
