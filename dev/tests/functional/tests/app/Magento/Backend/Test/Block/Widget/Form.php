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
     * Save button
     *
     * @var string
     */
    protected $saveButton;

    /**
     * Delete button
     *
     * @var string
     */
    protected $deleteButton;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->saveButton = '#save';
        $this->deleteButton = 'delete-button-button';
    }

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
    public function save(Fixture $fixture)
    {
        $this->_rootElement->find($this->saveButton, Locator::SELECTOR_CSS)->click();
        return $this;
    }

    /**
     * Delete current form item
     *
     * @param Fixture $fixture
     * @return Form
     */
    public function delete(Fixture $fixture)
    {
        $this->_rootElement->find($this->deleteButton, Locator::SELECTOR_ID)->click();
        return $this;
    }
}
