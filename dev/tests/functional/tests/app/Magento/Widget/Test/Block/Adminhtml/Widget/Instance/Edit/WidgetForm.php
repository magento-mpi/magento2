<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class Form
 * Widget Instance edit form
 */
class WidgetForm extends FormTabs
{
    /**
     * 'Continue' button locator
     *
     * @var string
     */
    protected $continueButton = '[data-ui-id="widget-button"]';

    /**
     * Click 'Continue' button
     *
     * @return void
     */
    public function clickContinue()
    {
        $this->_rootElement->find($this->continueButton)->click();
    }
}
