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
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Form
 * Is used to represent any form on the page
 *
 * @package Magento\Backend\Test\Block\Widget
 */
class Form extends Block
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
     * Fill the root form
     *
     * @param Fixture $fixture
     */
    public function fill(Fixture $fixture)
    {
        $dataSet = $fixture->getData();
        $fields = isset($dataSet['fields']) ? $dataSet['fields'] : array();
        foreach ($fields as $field => $value) {
            $this->_rootElement->find($field, Locator::SELECTOR_ID)->setValue($value);
        }
    }

    /**
     * Update the root form
     *
     * @param Fixture $fixture
     */
    public function update(Fixture $fixture)
    {
        $this->fill($fixture);
    }

    /**
     * Save the form
     */
    public function save(Fixture $fixture)
    {
        $this->_rootElement->find($this->saveButton, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Delete current form item
     */
    public function delete(Fixture $fixture)
    {
        $this->_rootElement->find($this->deleteButton, Locator::SELECTOR_ID)->click();
    }
}
