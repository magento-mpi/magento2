<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerBalance\Test\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab as AbstractTab;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Tab
 * Store credit tab
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
     * Field set
     *
     * @var string
     */
    protected $fieldSetStoreCredit = '#_customerbalancestorecreidt_fieldset';

    /**
     * Fill data to fields on tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $this->waitForElementVisible($this->fieldSetStoreCredit);
        $data = $this->dataMapping($fields);
        $this->_fill($data, $element);

        return $this;
    }

    /**
     * Check store credit balance history
     *
     * @param string $value
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
