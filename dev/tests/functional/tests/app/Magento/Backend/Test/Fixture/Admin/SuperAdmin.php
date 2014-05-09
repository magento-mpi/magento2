<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Fixture\Admin;

use Mtf\Fixture\DataFixture;

/**
 * Class SuperAdmin
 *
 */
class SuperAdmin extends DataFixture
{
    /**
     * initialize data
     */
    protected function _initData()
    {
        $config = $this->_configuration->getConfigParam('application/backend_user_credentials');
        $this->_data = array(
            'fields' => array(
                'username' => array(
                    'value' => $config['login']
                ),
                'password' => array(
                    'value' => $config['password']
                )
            )
        );
    }
}
