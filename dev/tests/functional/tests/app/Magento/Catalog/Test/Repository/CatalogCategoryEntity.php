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
 * Class CatalogCategoryEntity
 * Data for creation Category
 */
class CatalogCategoryEntity extends AbstractRepository
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
    }
}