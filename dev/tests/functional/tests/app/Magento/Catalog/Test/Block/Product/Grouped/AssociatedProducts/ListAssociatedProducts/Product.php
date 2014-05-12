<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Catalog\Test\Block\Product\Grouped\AssociatedProducts\ListAssociatedProducts;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

class Product extends Block
{
    /**
     * Fields mapping
     *
     * @var array
     */
    protected $mapping = array(
        'selection_qty' => "[data-column=qty] input"
    );

    /**
     * Fill product options
     *
     * @param string $qtyValue
     */
    public function fillQty($qtyValue)
    {
        $this->_rootElement->find($this->mapping['selection_qty'])->setValue($qtyValue);
    }
}
