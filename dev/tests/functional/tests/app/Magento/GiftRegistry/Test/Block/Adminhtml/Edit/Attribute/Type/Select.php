<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Edit\Attribute\Type;

use Mtf\Client\Element;

/**
 * Class Select
 * Filling select type attribute
 */
class Select extends AttributeForm
{
    /**
     * Fill specified form data
     *
     * @param array $fields
     * @param Element $element
     * @return void
     */
    protected function _fill(array $fields, Element $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($fields as $name => $field) {
            if (!isset($field['value'])) {
                $this->_fill($field);
            } else {
                if (is_array($field['value'])) {
                    $this->fillOptions($field['value']);
                } else {
                    $element = $this->getElement($context, $field);
                    if ($this->mappingMode || ($element->isVisible() && !$element->isDisabled())) {
                        $element->setValue($field['value']);
                        $this->setFields[$name] = $field['value'];
                    }
                }
            }
        }
    }
}
