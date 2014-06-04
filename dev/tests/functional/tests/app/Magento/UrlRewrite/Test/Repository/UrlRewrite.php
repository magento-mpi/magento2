<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class UrlRewrite
 * Data for creation url rewrite
 */
class UrlRewrite extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'request_path' => 'test-test-test%isolation%.html',
            'options' => 'No',
            'store_id' => 'Default Store View'
        ];
    }
}
