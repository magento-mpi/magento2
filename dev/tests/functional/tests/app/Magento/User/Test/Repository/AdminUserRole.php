<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class AdminUserRole
 *
 * @package Magento\User\Test\Repository
 */
class AdminUserRole extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'role_name' => 'RoleName%isolation%'
        ];
    }
}
