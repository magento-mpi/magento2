<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Fixture\Admin;

use Mtf\Fixture\DataFixture;

/**
 * Class SuperAdmin
 *
 * @package Magento\Backend\Test\Fixture\Admin
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
