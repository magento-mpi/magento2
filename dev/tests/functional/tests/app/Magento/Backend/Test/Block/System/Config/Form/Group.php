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
     * Fieldset selector
     *
     * @var string
     */
    protected $fieldset = 'fieldset';

    /**
     * Toggle link
     *
     * @var string
     */
    protected $toogleLink = '.entry-edit-head a';

    /**
     * Field element selector
     *
     * @var string
     */
    protected $element = '//*[@data-ui-id="%s"]';

    /**
     * Default checkbox selector
     *
     * @var string
     */
    protected $defaultCheckbox = '//*[@data-ui-id="%s"]/../../*[@class="use-default"]/input';

    /**
     * Open group fieldset
     */
    public function open()
    {
        if (!$this->_rootElement->find($this->fieldset)->isVisible()) {
            $this->_rootElement->find($this->toogleLink)->click();
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
            sprintf($this->element, $field),
            Element\Locator::SELECTOR_XPATH,
            $input
        );

        if ($element->isDisabled()) {
            $checkbox = $this->_rootElement->find(
                sprintf($this->defaultCheckbox, $field),
                Element\Locator::SELECTOR_XPATH,
                'checkbox'
            );
            $checkbox->setValue('No');
        }

        $element->setValue($value);
    }
}
