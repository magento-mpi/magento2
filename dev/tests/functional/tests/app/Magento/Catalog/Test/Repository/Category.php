<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Category Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class Category extends AbstractRepository
{
    /**
     * Attribute set for mapping data into ui tabs
     */
    const GROUP_GENERAL_INFORMATION = 'category_info_tabs_group_4';

    function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['men'] = array(
            'fields' => array(
                'name' => array(
                    'value' => 'Men %isolation%',
                    'group' => static::GROUP_GENERAL_INFORMATION
                ),
                'is_active' => array(
                    'value' => 'Yes',
                    'input_value' => '1',
                    'group' => static::GROUP_GENERAL_INFORMATION,
                    'input' => 'select'
                ),
                'include_in_menu' => array(
                    'value' => 'Yes',
                    'input_value' => '1',
                    'group' => static::GROUP_GENERAL_INFORMATION,
                    'input' => 'select'
                ),
                'available_sort_by' => array(
                    'value' => '',
                    'use_default' => true,
                    'group' => static::GROUP_GENERAL_INFORMATION,
                    'input' => 'multiselect'
                ),
                'default_sort_by' => array(
                    'value' => '',
                    'use_default' => true,
                    'group' => static::GROUP_GENERAL_INFORMATION,
                    'input' => 'select'
                ),
                'path' => array(
                    'value' => '',
                    'input_value' => '2',
                    'group' => static::GROUP_GENERAL_INFORMATION,
                    'input' => 'select'
                ),
            ),
            'category_path' =>  array(
                'value' => 'Default Category (0)',
                'input_value' => ''
            )
        );

        $this->_data['shoes'] = array(
            'fields' => array(
                'name' => array(
                    'value' => 'Subcategory-men-shoes %isolation%',
                    'group' => static::GROUP_GENERAL_INFORMATION
                ),
                'is_active' => array(
                    'value' => 'Yes',
                    'input_value' => '1',
                    'group' => static::GROUP_GENERAL_INFORMATION,
                    'input' => 'select'
                ),
                'include_in_menu' => array(
                    'value' => 'Yes',
                    'input_value' => '1',
                    'group' => static::GROUP_GENERAL_INFORMATION,
                    'input' => 'select'
                ),
                'available_sort_by' => array(
                    'value' => '',
                    'use_default' => true,
                    'group' => static::GROUP_GENERAL_INFORMATION,
                    'input' => 'multiselect'
                ),
                'default_sort_by' => array(
                    'value' => '',
                    'use_default' => true,
                    'group' => static::GROUP_GENERAL_INFORMATION,
                    'input' => 'select'
                ),
                'path' => array(
                    'value' => '',
                    'input_value' => '%men::getCategoryId%',
                    'group' => static::GROUP_GENERAL_INFORMATION,
                    'input' => 'select'
                ),
            ),
            'category_path' => array(
                'value' => 'Default Category (0)/' . '%men::getCategoryName%' .' (0)',
                'input_value' => ''
            )
        );
    }
}
