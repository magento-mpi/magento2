<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Customer\Test\Block\Adminhtml\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;

/**
 */
class AccountInformation extends Tab
{
    /**
     * Override method
     *
     * tests that relying on this form has random fail
     * the core of problem is onFocus event in js
     * we have to force this event with click
     *
     * see MAGETWO-31631 and MAGETWO-31121
     *
     * @param array   $fields
     * @param Element $element
     *
     * @throws \Exception
     */
    protected function _fill(array $fields, Element $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($fields as $name => $field) {
            if (!isset($field['value'])) {
                $this->_fill($field, $context);
            } else {
                $element = $this->getElement($context, $field);
                if ($this->mappingMode || ($element->isVisible() && !$element->isDisabled())) {
                    $element->setValue($field['value']);
                    $element->click();
                    $this->setFields[$name] = $field['value'];
                }
            }
        }
    }
}


