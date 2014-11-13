<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;

/**
 * General class for tabs on product FormTabs with "Add attribute" button.
 */
class ProductTab extends Tab
{
    /**
     * Attribute Search locator the Product page.
     *
     * @var string
     */
    protected $attributeSearch = '#product-attribute-search-container';

    /**
     * Selector for 'New Attribute' button.
     *
     * @var string
     */
    protected $newAttributeButton = '[id^="create_attribute"]';

    /**
     * Fixture mapping.
     *
     * @param array|null $fields
     * @param string|null $parent
     * @return array
     */
    protected function dataMapping(array $fields = null, $parent = null)
    {
        $this->placeholders = ['attribute_code' => $fields['custom_attribute']['value']['code']];
        $this->applyPlaceholders();
        return parent::dataMapping($fields, $parent);
    }

    /**
     * Click on 'New Attribute' button.
     *
     * @return void
     */
    public function addNewAttribute()
    {
        $this->_rootElement->find($this->attributeSearch)->click();
        $this->_rootElement->find($this->newAttributeButton)->click();
    }
}
