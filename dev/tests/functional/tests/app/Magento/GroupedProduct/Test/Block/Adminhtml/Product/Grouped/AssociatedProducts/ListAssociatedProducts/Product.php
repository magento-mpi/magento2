<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GroupedProduct\Test\Block\Adminhtml\Product\Grouped\AssociatedProducts\ListAssociatedProducts;

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
        $mapping = $this->dataMapping($fields);
        $newFields = $this->_getData($mapping);
        $newFields['name'] = $this->_rootElement->find('td.col-name')->getText();
        return $newFields;
    }
}
