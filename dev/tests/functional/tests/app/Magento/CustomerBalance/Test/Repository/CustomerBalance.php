<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerBalance\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CustomerBalance
 * Customer Balance repository
 */
class CustomerBalance extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['customerBalance_5'] = [
            'balance_delta' => 5,
            'website_id' => ['dataSet' => 'main_website'],
            'additional_info' => 'Some comment',
        ];

        $this->_data['customerBalance_100'] = [
            'balance_delta' => 100,
            'website_id' => ['dataSet' => 'main_website'],
            'additional_info' => 'Some comment',
        ];
    }
}
