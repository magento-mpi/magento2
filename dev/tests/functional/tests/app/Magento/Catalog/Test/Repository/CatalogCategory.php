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
        $this->_data['default_subcategory'] = [
            'name' => 'Subcategory%isolation%',
            'path' => 'Default Category',
            'parent_id' => 2,
            'is_active' => 'Yes',
            'include_in_menu' => 'Yes',
        ];

        $this->_data['root_category'] = [
            'name' => 'Category%isolation%',
            'path' => '1/3',
            'is_active' => 'Yes',
            'include_in_menu' => 'Yes'
        ];

        $this->_data['root_subcategory'] = [
            'name' => 'Category%isolation%',
            'path' => '1/3/4',
            'is_active' => 'Yes',
            'include_in_menu' => 'Yes'
        ];
    }
}
