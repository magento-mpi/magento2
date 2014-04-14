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
     * Attribute option row
     *
     * @var string
     */
    protected $optionRow = '//*[@id="manage-options-panel"]//tbody//tr[%row%]';

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
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        parent::fill($fixture, $element);
        $this->fillOptions($fixture);

        return $this;
    }

    /**
     * Fill attribute options
     *
     * @param FixtureInterface $fixture
     */
    protected function fillOptions(FixtureInterface $fixture)
    {
        /** @var $fixture \Magento\Catalog\Test\Fixture\ProductAttribute */
        $options = $fixture->getOptions();

        $row = 1;
        foreach ($options as $option) {
            $this->_rootElement->find($this->addNewOption)->click();
            // TODO: implement filling for any number of stores
            $this->_rootElement->find(
                str_replace('%row%', $row, $this->optionRow) . '/td[2]/input',
                Element\Locator::SELECTOR_XPATH,
                'checkbox'
            )->setValue($option['default']['value']);
            $this->_rootElement->find(
                str_replace('%row%', $row, $this->optionRow) . '/td[3]/input',
                Element\Locator::SELECTOR_XPATH
            )->setValue($option['label']['value']);
            ++$row;
        }
    }
}
