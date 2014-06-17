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
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
