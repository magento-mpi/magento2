<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Widget;

use Mtf\Fixture;
use Mtf\Client\Element\Locator;
use Mtf\Block\Form as FormInstance;

/**
 * Class Form
 * Is used to represent any form on the page
 *
 * @package Magento\Backend\Test\Block\Widget
 */
class Form extends FormInstance
{
    /**
     * 'Save' button
     *
     * @var string
     */
    protected $saveButton = '#save';

    /**
     * 'Save And Continue Edit' button
     *
     * @var string
     */
    protected $saveAndContinueButton = '#save_and_continue';

    /**
     * 'Save And Continue Edit' button
     *
     * @var string
     */
    protected $saveAndContinueEditButton = '#save_and_continue_edit';

    /**
     * Back button
     *
     * @var string
     */
    protected $backButton = '#back';

    /**
     * Reset button
     *
     * @var string
     */
    protected $resetButton = '#reset';

    /**
     * 'Delete' button
     *
     * @var string
     */
    protected $deleteButton = '#delete-button-button';

    /**
     * Update the root form
     *
     * @param Fixture $fixture
     * @return Form
     */
    public function update(Fixture $fixture)
    {
        $this->fill($fixture);
        return $this;
    }

    /**
     * Save the form
     *
     * @param Fixture $fixture
     * @return Form
     */
    public function save(Fixture $fixture = null)
    {
        $this->_rootElement->find($this->saveButton, Locator::SELECTOR_CSS)->click();
        return $this;
    }

    /**
     * Back action
     *
     * @return Form
     */
    public function back()
    {
        $this->_rootElement->find($this->backButton, Locator::SELECTOR_CSS)->click();
        return $this;
    }

    /**
     * Reset the form
     *
     * @return Form
     */
    public function reset()
    {
        $this->_rootElement->find($this->resetButton, Locator::SELECTOR_CSS)->click();
        return $this;
    }

    /**
     * Delete current form item
     *
     * @param Fixture $fixture
     * @return Form
     */
    public function delete(Fixture $fixture = null)
    {
        $this->_rootElement->find($this->deleteButton, Locator::SELECTOR_CSS)->click();
        return $this;
    }

    /**
     * Click save and continue button on form
     */
    public function clickSaveAndContinue()
    {
        $this->_rootElement->find($this->saveAndContinueButton, Locator::SELECTOR_CSS)->click();
        return $this;
    }

    /**
     * Click save and continue button on form
     */
    public function clickSaveAndContinueEdit()
    {
        $this->_rootElement->find($this->saveAndContinueEditButton, Locator::SELECTOR_CSS)->click();
        return $this;
    }
}
