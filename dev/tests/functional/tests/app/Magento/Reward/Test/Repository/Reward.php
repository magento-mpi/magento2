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
    }
}
