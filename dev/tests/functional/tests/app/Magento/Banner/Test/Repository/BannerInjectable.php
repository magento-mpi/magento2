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
            'store_contents_not_use' => ['value_0' => 'No'],
            'store_contents' => ['value_0' => 'banner_content_%isolation%']
        ];
    }
}
