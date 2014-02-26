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

namespace Magento\Catalog\Test\Block\Backend\Product\Attribute;

use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Form;

/**
 * Product attribute edit page
 *
 * @package Magento\Catalog\Test\Block
 */
class Edit extends Form
{
    /**
     * Frontend properties selector
     *
     * @var string
     */
    protected $frontendProperties = '#front_fieldset-wrapper .title';

    /**
     * Save attribute selector
     *
     * @var string
     */
    protected $saveAttribute = '[data-ui-id="attribute-edit-content-save-button"]';

    /**
     * 'Add new option' button selector
     *
     * @var string
     */
    protected $addNewOption = '#add_new_option_button';

    /**
     * Open frontend properties
     */
    public function openFrontendProperties()
    {
        $this->_rootElement->find($this->frontendProperties)->click();
    }

    /**
     * Save attribute
     */
    public function saveAttribute()
    {
        $this->_rootElement->find($this->saveAttribute)->click();
    }

    /**
     * Fill form with attribute options
     *
     * @param FixtureInterface $fixture
     * @param null|Element $element
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        parent::fill($fixture, $element);
        /** @var $fixture \Magento\Catalog\Test\Fixture\ProductAttribute */
        $options = $fixture->getOptions();
        foreach ($options as $option) {
            $this->fillOption($option);
        }
    }

    /**
     * Fill new option
     *
     * @param array $option
     */
    protected function fillOption(array $option)
    {
        $this->_rootElement->find($this->addNewOption)->click();
        $fields = $this->dataMapping($option);
        $this->_fill($fields);
    }
}
