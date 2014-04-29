<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\AdvancedPricingTab;

use Mtf\Client\Element;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options;

/**
 * Class OptionField
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Product\Edit\AdvancedPricingTab
 */
class OptionTier extends Options
{
    /**
     * Add button selector
     *
     * @var string
     */
    private $buttonFormLocator = "button[title='Add Tier']";

    /**
     * Fill the form
     *
     * @param array $fields
     * @param array $locatorPlaceholder
     * @param Element $element
     * @return $this
     */
    public function fillAnArray(array $fields, array $locatorPlaceholder = [], Element $element = null)
    {
        $this->_rootElement->find($this->buttonFormLocator)->click();
        return parent::fillAnArray($fields);
    }
} 