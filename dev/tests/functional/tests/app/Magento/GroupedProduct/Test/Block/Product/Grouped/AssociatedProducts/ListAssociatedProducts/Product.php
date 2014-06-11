<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Block\Product\Grouped\AssociatedProducts\ListAssociatedProducts;

use Mtf\Block\Form;

/**
 * Class Product
 * Assigned product row to grouped option
 */
class Product extends Form
{
    /**
     * Fill product options
     *
     * @param string $qtyValue
     * @return void
     */
    public function fillOption($qtyValue)
    {
        $mapping = $this->dataMapping($qtyValue);
        $this->_fill($mapping);
    }

    /**
     * Get product options
     *
     * @param array $fields
     * @return array
     */
    public function getOption(array $fields)
    {
        $mapping = $this->dataMapping(['qty' => $fields['qty'], 'sku' => $fields['search_data']['sku']]);
        $newFields = $this->_getData(['qty' => $mapping['qty']]);
        $newFields['search_data']['sku'] = $this->_rootElement->find($mapping['sku']['selector'])->getText();
        return $newFields;
    }
}
