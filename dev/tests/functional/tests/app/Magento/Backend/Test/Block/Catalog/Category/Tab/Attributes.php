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

namespace Magento\Backend\Test\Block\Catalog\Category\Tab;

use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Attributes
 * Catalog Category Attributes per Group Tab block
 *
 * @package Magento\Backend\Test\Block\Catalog\Category\Tab
 */
class Attributes extends Tab
{
    /**
     * Fixture mapping for category data
     *
     * @param array $fields
     * @return array
     */
    protected function dataMapping(array $fields)
    {
        $categoryFields = array();
        foreach ($fields as $key => $field) {
            $customField = str_replace('category_info_tabs_', '', $field['group']) . $key;
            $categoryFields[$customField] = $fields[$key];
        }

        return parent::dataMapping($categoryFields);
    }
}
