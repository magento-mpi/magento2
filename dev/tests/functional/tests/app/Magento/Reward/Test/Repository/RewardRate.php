<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class RewardRate
 * Reward Rate repository
 */
class RewardRate extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['rate_points_to_currency'] = [
            'website_id' => ['dataSet' => 'Main Website'],
            'customer_group_id' => ['dataSet' => 'All Customer Groups'],
            'direction' => 'Points to Currency',
            'value' => 10,
            'equal_value' => 1
        ];

        $this->_data['rate_currency_to_points'] = [
            'website_id' => ['dataSet' => 'Main Website'],
            'customer_group_id' => ['dataSet' => 'All Customer Groups'],
            'direction' => 'Currency to Points',
            'value' => 10,
            'equal_value' => 1
        ];
    }
}
