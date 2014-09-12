<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class BannerInjectable
 * Data for creation banner
 */
class BannerInjectable extends AbstractRepository
{
    /**
     * Constructor
     *
     * @param array $defaultConfig [optional]
     * @param array $defaultData [optional]
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'name' => 'banner_%isolation%',
            'is_enabled' => 'Yes',
            'use_customer_segment' => 'All',
            'store_contents_not_use' => ['value_1' => 'Yes'],
            'store_contents' => ['value_0' => 'banner_content_%isolation%']
        ];

        $this->_data['banner_rotator_shopping_cart_rules'] = [
            'name' => 'banner_%isolation%',
            'is_enabled' => 'Yes',
            'use_customer_segment' => 'All',
            'store_contents_not_use' => ['value_1' => 'Yes'],
            'store_contents' => ['value_0' => 'banner_content_%isolation%'],
            'banner_sales_rules' => ['presets' => 'default']
        ];

        $this->_data['banner_rotator_catalog_rules'] = [
            'name' => 'banner_%isolation%',
            'is_enabled' => 'Yes',
            'use_customer_segment' => 'All',
            'store_contents_not_use' => ['value_1' => 'Yes'],
            'store_contents' => ['value_0' => 'banner_content_%isolation%'],
            'banner_catalog_rules' => ['presets' => 'default']
        ];
    }
}
