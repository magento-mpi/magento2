<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Repository;

use Mtf\Factory\Factory;
use Mtf\Repository\AbstractRepository;

class AdminUser extends AbstractRepository
{

    /**
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['admin_default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        $this->_data['user_with_restricted_resource'] = $this->_getUserWithRestrictedResource();
    }

    /**
     * @return array
     */
    protected function _getUserWithRestrictedResource()
    {
        $role = array(
            'data' => array(
                'fields' => array(
                    'roles' => array(
                        'value' => array('%role_id%')
                    )
                )
            )
        );

        return array_replace_recursive($this->_data['admin_default'], $role);
    }
}