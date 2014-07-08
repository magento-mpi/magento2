<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleShopping\Test\Block\Adminhtml\Types\Edit;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class GoogleShoppingForm
 * Google Shopping form
 */
class GoogleShoppingForm extends Form
{
    /**
     * Fill specified form data
     *
     * @param array $fields
     * @param Element $element
     */
    protected function _fill(array $fields, Element $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($fields as $name => $field) {
            if (!isset($field['value'])) {
                $this->_fill($field);
            } else {
                $element = $this->getElement($context, $field);
                if ($this->mappingMode || ($element->isVisible() && !$element->isDisabled())) {
                    $element->setValue($field['value']);
                    $this->setFields[$name] = $field['value'];
                    $this->waitForElementNotVisible('ancestor::body//div[@id="loading-mask"]', Locator::SELECTOR_XPATH);
                }
            }
        }
    }

    /**
     * Find Attribute in Attribute set mapping form
     *
     * @param string $deletedAttribute
     * @return bool
     */
    public function findAttribute($deletedAttribute)
    {
        $attributes = $this->getOptions();
        foreach ($attributes as $attribute) {
            if ($attribute == $deletedAttribute) {
                return true;
            }
        }

        return false;
    }

    /**
     * Click "Add New Attribute" button
     *
     * @return void
     */
    public function clickAddNewAttribute()
    {
        $this->_rootElement->find('#add_new_attribute')->click();
    }

    /**
     * Getting all options in select list
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [];
        $counter = 1;
        $newOption = $this->_rootElement->find(
            sprintf('//select[@id="gcontent_attribute_0_attribute"]//option[%d]', $counter),
            Locator::SELECTOR_XPATH
        );
        while ($newOption->isVisible()) {
            $options[] = $newOption->getText();
            $counter++;
            $newOption = $this->_rootElement->find(
                sprintf('//select[@id="gcontent_attribute_0_attribute"]//option[%d]', $counter),
                Locator::SELECTOR_XPATH
            );
        }

        return $options;
    }
}
