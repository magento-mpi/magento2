<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Website
 * Data for creation Website
 */
class Website extends AbstractRepository
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
        $this->_data['all_websites'] = [
            'name' => 'All Websites',
            'website_id' => 0,
        ];

        $this->_data['main_website'] = [
            'name' => 'Main Website',
            'code' => 'base',
            'sort_order' => 0,
            'website_id' => 1,
        ];
    }
}
