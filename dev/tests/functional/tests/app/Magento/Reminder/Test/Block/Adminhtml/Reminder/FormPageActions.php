<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reminder\Test\Block\Adminhtml\Reminder;

/**
 * Page actions block on reminder view page(backend).
 */
class FormPageActions extends \Magento\Backend\Test\Block\FormPageActions
{
    /**
     * Locator for "Run Now" button.
     *
     * @var string
     */
    protected $runNowButton = '#run_now';

    /**
     * Click on "Run Now" button.
     *
     * @return void
     */
    public function runNow()
    {
        $this->_rootElement->find($this->runNowButton)->click();
        $this->_rootElement->acceptAlert();
    }
}
