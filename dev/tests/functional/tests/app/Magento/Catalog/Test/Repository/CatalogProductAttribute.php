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
 * Class CatalogProductAttribute
 * Data for creation Product Attributes
 */
class CatalogProductAttribute extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['attribute_type_text_field'] = [
            'frontend_label' => 'attribute_text%isolation%',
            'frontend_input' => 'Text Field',
            'is_required' => 'No'
        ];

        $this->_data['attribute_type_dropdown'] = [
            'frontend_label' => 'attribute_dropdown%isolation%',
            'frontend_input' => 'Dropdown',
            'is_required' => 'No'
        ];
    }
}
