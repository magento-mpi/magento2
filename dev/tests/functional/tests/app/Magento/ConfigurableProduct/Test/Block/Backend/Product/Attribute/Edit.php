<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Backend\Product\Attribute;

use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;
use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Form;
use Mtf\Factory\Factory;

/**
 * Product attribute edit page
 *
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
     * Fill attribute options
     *
     * @param $data
     */
    public function fillAttributeOption($data)
    {
        $this->_rootElement->find('#attribute_label')
            ->setValue($data['attribute_options']['attribute_label']);
        $this->_rootElement->find('#frontend_input', Element\Locator::SELECTOR_CSS, 'select')
            ->setValue($data['attribute_options']['frontend_input']);
        $this->_rootElement->find('#is_required', Element\Locator::SELECTOR_CSS, 'select')
            ->setValue($data['attribute_options']['is_required']);

        $addButton = $this->_rootElement->find('#add_new_option_button');
        $table = $this->_rootElement->find('.data-table');
        foreach ($data['attribute_options']['options'] as $index => $value) {
            $addButton->click();
            $table->find('[name="' . $index . '"]')->setValue($value);
        }
        $this->saveAttribute();
    }
}
