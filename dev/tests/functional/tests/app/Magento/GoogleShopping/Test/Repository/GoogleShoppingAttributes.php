<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleShopping\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class GoogleShoppingAttributes
 * Data for creation Google Shopping Attributes
 */
class GoogleShoppingAttributes extends AbstractRepository
{
    /**
     * Construct
     *
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'target_country' => 'United States',
            'attribute_set_id' => ['dataSet' => 'default'],
            'category' => 'Apparel & Accessories',
        ];
    }
}
