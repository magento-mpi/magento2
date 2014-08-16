<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Composite;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class Configure
 * Adminhtml catalog product composite configure block
 *
 */
class Configure extends Form
{
    /**
     * Fill options for the product
     *
     * @param array $productOptions
     * @return void
     */
    public function fillOptions(array $productOptions)
    {
        foreach ($productOptions as $option) {
            $select = $this->_rootElement->find(
                '//div[@class="product-options"]//label[text()="' .
                $option['title'] .
                '"]//following-sibling::*//select',
                Locator::SELECTOR_XPATH,
                'select'
            );
            $select->setValue($option['value']);
        }
        $this->_rootElement->find('.ui-dialog-buttonset button:nth-of-type(2)')->click();
    }
}
