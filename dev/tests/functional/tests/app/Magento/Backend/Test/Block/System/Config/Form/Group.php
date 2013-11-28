<?php
/**
 * Store configuration group
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\System\Config\Form;

use \Magento\Backend\Test\Block\Widget\Form;
use Mtf\Client\Element;

class Group extends Form
{
    /**
     * Open group fieldset
     */
    public function open()
    {
        if (!$this->_rootElement->find('fieldset')->isVisible()) {
            $this->_rootElement->find('.entry-edit-head a')->click();
        }
    }

    /**
     * Set store configuration value by element data-ui-id
     *
     * @param string $field
     * @param mixed $value
     */
    public function setValue($field, $value)
    {
        $input = null;
        $fieldParts = explode('-', $field);
        if (in_array($fieldParts[0], array('select', 'checkbox'))) {
            $input = $fieldParts[0];
        }

        $element = $this->_rootElement->find(
            '//*[@data-ui-id="' . $field . '"]', Element\Locator::SELECTOR_XPATH, $input
        );

        if ($element->isDisabled()) {
            $checkbox = $this->_rootElement->find(
                '//*[@data-ui-id="' . $field . '"]/../../*[@class="use-default"]/input',
                //*[@id="general_locale_code"]
                Element\Locator::SELECTOR_XPATH,
                'checkbox'
            );
            $checkbox->setValue('No');
        }

        $element->setValue($value);
    }
}