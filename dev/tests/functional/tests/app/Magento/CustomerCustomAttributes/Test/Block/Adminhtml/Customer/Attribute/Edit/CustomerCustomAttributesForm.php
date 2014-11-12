<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\Edit;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Customer Attribute form.
 */
class CustomerCustomAttributesForm extends FormTabs
{
    /**
     * Mage error selector.
     *
     * @var string
     */
    protected $mageError = '//*[contains(@for,"attribute_code")]';

    /**
     * Get customer attribute error.
     *
     * @return array
     */
    public function getAttributeError()
    {
        $data = [];
        list($label, $error) = $this->_rootElement->find($this->mageError, Locator::SELECTOR_XPATH)->getElements();
        $data['label'] = $label->getText();
        $data['text'] = $error->getText();

        return $data;
    }
}
