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

namespace Magento\User\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Mtf\System\Config;

/**
 * Class Category
 *
 * @package Magento\User\Test\Fixture
 */
class Role extends DataFixture
{
    /**
     * @return $this
     */
    public function persist()
    {
        return Factory::getApp()->magentoUserCreateRole($this);
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'all' => array(
                    'value' => 0,
                ),
                'gws_is_all' => array(
                    'value' => 1,
                ),
                'rolename' => array(
                    'value' => 'auto%isolation%',
                ),
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoUserResource($this->_dataConfig, $this->_data);
    }
}
