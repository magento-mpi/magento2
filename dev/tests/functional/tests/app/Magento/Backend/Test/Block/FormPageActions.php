<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block;

/**
 * Class FormPageActions
 * Form page actions block
 *
 * @package Magento\Backend\Test\Block
 */
class FormPageActions extends PageActions
{
    /**
     * "Back" button
     *
     * @var string
     */
    protected $backButton = '#back';

    /**
     * "Reset" button
     *
     * @var string
     */
    protected $resetButton = '#reset';

    /**
     * "Save and Continue Edit" button
     *
     * @var string
     */
    protected $saveAndContinueButton = '#save_and_continue';

    /**
     * "Save" button
     *
     * @var string
     */
    protected $saveButton = '#save';

    /**
     * Click on "Back" button
     */
    public function back()
    {
        $this->_rootElement->find($this->backButton)->click();
    }

    /**
     * Click on "Reset" button
     */
    public function reset()
    {
        $this->_rootElement->find($this->resetButton)->click();
    }

    /**
     * Click on "Save and Continue Edit" button
     */
    public function saveAndContinue()
    {
        $this->_rootElement->find($this->saveAndContinueButton)->click();
    }

    /**
     * Click on "Save" button
     */
    public function save()
    {
        $this->_rootElement->find($this->saveButton)->click();
        $this->waitForElementNotVisible('.popup popup-loading');
        $this->waitForElementNotVisible('.loader');
    }
}
