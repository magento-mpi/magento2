<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Backend\Order;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class for configure product form on order create page in backend
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class ConfigureProduct extends Form
{
    /**
     * Fill options for the product
     *
     * @param array $productOptions
     */
    public function fillOptions($productOptions)
    {
        foreach ($productOptions as $attributeLabel => $attributeValue) {
            $select = $this->_rootElement->find(
                '//div[@class="product-options"]//label[text()="' .
                $attributeLabel .
                '"]//following-sibling::*//select',
                Locator::SELECTOR_XPATH,
                'select'
            );
            $select->setValue($attributeValue);
        }
        $this->_rootElement->find('.ui-dialog-buttonset button:nth-of-type(2)')->click();
    }
}
