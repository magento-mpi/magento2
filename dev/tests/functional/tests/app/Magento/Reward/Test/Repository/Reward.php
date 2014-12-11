<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reward\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Reward
 * Reward points repository
 */
class Reward extends AbstractRepository
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
        $this->_data['reward_points_50'] = [
            'points_delta' => 50,
        ];

        $this->_data['reward_points_150'] = [
            'points_delta' => 150,
        ];
    }
}
