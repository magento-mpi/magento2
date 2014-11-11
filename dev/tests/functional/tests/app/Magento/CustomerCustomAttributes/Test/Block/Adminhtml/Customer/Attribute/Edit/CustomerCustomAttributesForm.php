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
    protected $mageError = '//*[contains(@class,"field ")][.//*[contains(@class,"mage-error")]]';

    /**
     * Get Require Notice Properties.
     *
     * @return array
     */
    public function getRequireNoticeProperties()
    {
        $data = [];
        $elements = $this->_rootElement->find($this->mageError, Locator::SELECTOR_XPATH)->getElements();
        foreach ($elements as $element) {
            $data[$element->find('label')->getText()] = $element;
        }
        return $data;
    }
}
