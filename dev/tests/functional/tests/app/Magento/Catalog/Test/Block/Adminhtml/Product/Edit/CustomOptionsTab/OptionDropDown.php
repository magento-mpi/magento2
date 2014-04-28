<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\CustomOptionsTab;

use Mtf\Client\Element;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options;

/**
 * Class OptionDropDown
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Product\Edit\CustomOptionsTab
 */
class OptionDropDown extends Options
{
    /**
     * Add button selector
     *
     * @var string
     */
    private $buttonFormLocator = '#product_option_%row-1%_add_select_row';

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
        $this->buttonFormLocator = strtr($this->buttonFormLocator, $locatorPlaceholder);
        $this->_rootElement->find($this->buttonFormLocator)->click();
        return parent::fillAnArray($fields);
    }
} 