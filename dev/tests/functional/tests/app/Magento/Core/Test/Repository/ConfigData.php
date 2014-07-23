<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class ConfigData
 * Data for creation Config settings
 */
class ConfigData extends AbstractRepository
{
    /**
     * Constructor
     *
     * @param array $defaultConfig [optional]
     * @param array $defaultData [optional]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['reward_purchase'] = [
            'section' => [
                [
                    'path' => 'magento_reward/points/order',
                    'scope' => 'magento_reward',
                    'scope_id' => '1',
                    'value' => '1',
                ]
            ]
        ];
    }
}
