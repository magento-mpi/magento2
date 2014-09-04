<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
            'website_id' => 'Main Website',
            'additional_info' => 'Some comment',
        ];
    }
}
