<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product;

use Mtf\Client\Element;
use Magento\Catalog\Test\Block\Adminhtml\Product\Form as ParentForm;
/**
 * Class ProductForm
 * Product form on backend product page
 */
class Form extends ParentForm
{
    /**
     * Filling variations field and returning $attribute Name if he is present
     *
     * @param string $attributeName
     * @return string
     */
    public function fillSearchAttribute($attributeName)
    {
        $this->_rootElement->find('#configurable-attribute-selector')->setValue($attributeName);
        $attributeName = $this->_rootElement->find('.mage-suggest-dropdown a.ui-corner-all')->getText();

        return $attributeName;
    }
}
