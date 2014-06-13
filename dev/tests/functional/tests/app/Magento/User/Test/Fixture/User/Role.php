<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Fixture\User;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlTransport;
use Magento\User\Test\Fixture\AdminUserRole;

/**
 * Class Role
 *
 * Data keys:
 *  - dataSet
 */
class Role implements FixtureInterface
{
    /**
     * Admin User Role
     *
     * @var AdminUserRole
     */
    protected $role;

    /**
     * User role name
     *
     * @var string
     */
    protected $data;

    /**
     * @construct
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataSet']) && $data['dataSet'] !== '-') {
            $this->role = $fixtureFactory->createByCode('adminUserRole', ['dataSet' => $data['dataSet']]);
            $this->data = $this->role->getRoleName();
        }
    }

    /**
     * Persist user role
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param $key [optional]
     * @return string|null
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return role fixture
     *
     * @return AdminUserRole
     */
    public function getRole()
    {
        return $this->role;
    }
}
