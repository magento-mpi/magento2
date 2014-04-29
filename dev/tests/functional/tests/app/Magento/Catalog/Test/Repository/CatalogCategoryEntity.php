<?php
/**
 * Created by PhpStorm.
 * User: oonoshko
 * Date: 23.04.14
 * Time: 11:58
 */

namespace Magento\Catalog\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogProductSimple
 *
 * @package Magento\Catalog\Test\Repository
 */
class CatalogCategoryEntity extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default_subcategory'] = [
            'name' => 'Subcategory%isolation%',
            'path' => '1/2',
            'is_active' => '1',
            'include_in_menu' => '1'
        ];
    }
}