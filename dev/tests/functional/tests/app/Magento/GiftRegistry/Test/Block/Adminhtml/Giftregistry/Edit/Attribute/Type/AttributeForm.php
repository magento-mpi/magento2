<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Giftregistry\Edit\Attribute\Type;

use Mtf\Client\Element;
use Mtf\Block\Form;

/**
 * Class AttributeForm
 * Responds for filling attribute form
 */
abstract class AttributeForm extends Form
{
    /**
     * Add new option button selector
     *
     * @var string
     */
    protected $addNewOption = '[id^="registry_add_select_row_button"]';

    /**
     * Options selector
     *
     * @var string
     */
    protected $optionSelector = '//tr[contains(@id,"registry_attribute") and contains(@id,"select")][last()]';

    /**
     * Filling attribute form
     *
     * @param array $attributeFields
     * @param Element $element
     * @return void
     */
    public function fillForm(array $attributeFields, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($attributeFields);
        $this->_fill($mapping, $element);
    }

    /**
     * Getting options data form on the product form
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function getDataOptions(array $fields = null, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($fields);
        return $this->_getData($mapping, $element);
    }
}