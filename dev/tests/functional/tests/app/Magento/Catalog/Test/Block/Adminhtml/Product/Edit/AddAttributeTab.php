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
 * General class for tabs with "Add attribute" button.
 */
class AddAttributeTab extends Tab
{
    /**
     * Fixture mapping.
     *
     * @param array|null $fields
     * @param string|null $parent
     * @return array
     */
    protected function dataMapping(array $fields = null, $parent = null)
    {
        $mappingFields = ($parent !== null) ? $parent : $this->mapping;
        $mapping = parent::dataMapping($fields, $parent);
        if (isset($mapping['custom_attribute'])) {
            $mapping['custom_attribute']['selector'] = sprintf(
                $mappingFields['custom_attribute']['selector'],
                $fields['custom_attribute']['value']['code']
            );
        }
        return $mapping;
    }
}
