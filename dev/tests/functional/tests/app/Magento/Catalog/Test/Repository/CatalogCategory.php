<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogCategory
 * Data for creation Category
 */
class CatalogCategory extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default_category'] = [
            'name' => 'Default Category',
            'parent_id' => 1,
            'is_active' => 'Yes',
        ];

        $this->_data['default_subcategory'] = [
            'name' => 'Subcategory%isolation%',
            'url_key' => 'Subcategory%isolation%',
            'parent_id' => ['dataSet' => 'default_category'],
            'is_active' => 'Yes',
            'include_in_menu' => 'Yes',
        ];

        $this->_data['root_category'] = [
            'name' => 'Category%isolation%',
            'url_key' => 'Subcategory%isolation%',
            'parent_id' => 1,
            'is_active' => 'Yes',
            'include_in_menu' => 'Yes'
        ];

        $this->_data['root_subcategory'] = [
            'name' => 'Category%isolation%',
            'url_key' => 'Subcategory%isolation%',
            'parent_id' => ['dataSet' => 'root_category'],
            'is_active' => 'Yes',
            'include_in_menu' => 'Yes'
        ];
    }
}
