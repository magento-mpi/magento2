<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class GiftWrapping Repository
 */
class GiftWrapping extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['enabled'] = [
            'design' => 'GiftWrapping%isolation%',
            'website_ids' => ['Main Website'],
            'status' => 'Enabled',
            'base_price' => '10',
        ];

        $this->_data['disabled'] = [
            'design' => 'Gift Wrapping%isolation%',
            'website_ids' => ['Main Website'],
            'status' => 'Disabled',
            'base_price' => '10',
        ];
    }
}
