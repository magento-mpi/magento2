<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Block\Adminhtml\Edit;

use Magento\Backend\Test\Block\Widget\Tab as AbstractTab;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Form
 * Form for creation of the customer
 */
class Tab extends AbstractTab
{
    /**
     * Store credit balance XPath
     *
     * @var string
     */
    protected $storeCreditBalance = './/*[@id="Store_Credit"]//*[@data-column="amount"]';

    /**
     * Fill data to fields on tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $this->waitForElementVisible('#_customerbalancestorecreidt_fieldset');
        $data = $this->dataMapping($fields);
        $this->_fill($data, $element);

        return $this;
    }

    /**
     * Check store credit balance history
     *
     * @param $value
     * @return bool
     */
    public function isStoreCreditBalance($value)
    {
        $this->waitForElementVisible($this->storeCreditBalance, Locator::SELECTOR_XPATH);
        return $this->_rootElement
            ->find(sprintf($this->storeCreditBalance . '[contains(.,"%s")]', $value), Locator::SELECTOR_XPATH)
            ->isVisible();
    }
}
