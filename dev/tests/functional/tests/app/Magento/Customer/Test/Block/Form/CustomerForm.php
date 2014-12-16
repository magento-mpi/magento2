<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Customer\Test\Block\Form;

use Mtf\Block\Form;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Client\Element;

/**
 * Customer account edit form.
 */
class CustomerForm extends Form
{
    /**
     * Save button button css selector.
     *
     * @var string
     */
    protected $saveButton = '[type="submit"]';

    /**
     * Locator for customer attribute on Edit Account Information page.
     *
     * @var string
     */
    protected $customerAttribute = "[name='%s[]']";

    /**
     * Validation text message for a field.
     *
     * @var string
     */
    protected $validationText = '.mage-error[for="%s"]';

    /**
     * Click on save button.
     *
     * @return void
     */
    public function submit()
    {
        $this->_rootElement->find($this->saveButton)->click();
    }

    /**
     * Get all error validation messages for fields.
     *
     * @param CustomerInjectable $customer
     * @return array
     */
    public function getValidationMessages(CustomerInjectable $customer)
    {
        $messages = [];
        foreach (array_keys($customer->getData()) as $field) {
            $element = $this->_rootElement->find(sprintf($this->validationText, str_replace('_', '-', $field)));
            if ($element->isVisible()) {
                $messages[$field] = $element->getText();
            }
        }

        return $messages;
    }

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
                    // it is important to do click!
                    $element->click();
                    $this->setFields[$name] = $field['value'];
                }
            }
        }
    }
}
