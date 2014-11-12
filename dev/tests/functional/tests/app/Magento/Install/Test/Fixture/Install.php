<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class Install
 */
class Install extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Install\Test\Repository\Install';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Install\Test\Handler\Install\InstallInterface';

    protected $defaultDataSet = [
    ];

    protected $dbHost = [
        'attribute_code' => 'dbHost',
        'backend_type' => 'virtual',
    ];

    protected $dbUser = [
        'attribute_code' => 'dbUser',
        'backend_type' => 'virtual',
    ];

    protected $dbPassword = [
        'attribute_code' => 'dbPassword',
        'backend_type' => 'virtual',
    ];

    protected $dbName = [
        'attribute_code' => 'dbName',
        'backend_type' => 'virtual',
    ];

    protected $web = [
        'attribute_code' => 'web',
        'backend_type' => 'virtual',
    ];

    protected $admin = [
        'attribute_code' => 'admin',
        'backend_type' => 'virtual',
    ];

    protected $adminUsername = [
        'attribute_code' => 'adminUsername',
        'backend_type' => 'virtual',
    ];

    protected $adminEmail = [
        'attribute_code' => 'adminEmail',
        'backend_type' => 'virtual',
    ];

    protected $adminPassword = [
        'attribute_code' => 'adminPassword',
        'backend_type' => 'virtual',
    ];

    protected $adminConfirm = [
        'attribute_code' => 'adminConfirm',
        'backend_type' => 'virtual',
    ];

    public function getDbHost()
    {
        return $this->getData('dbHost');
    }

    public function getDbUser()
    {
        return $this->getData('dbUser');
    }

    public function getDbPassword()
    {
        return $this->getData('dbPassword');
    }

    public function getDbName()
    {
        return $this->getData('dbName');
    }

    public function getWeb()
    {
        return $this->getData('web');
    }

    public function getAdmin()
    {
        return $this->getData('admin');
    }

    public function getAdminUsername()
    {
        return $this->getData('adminUsername');
    }

    public function getAdminEmail()
    {
        return $this->getData('adminEmail');
    }

    public function getAdminPassword()
    {
        return $this->getData('adminPassword');
    }

    public function getAdminConfirm()
    {
        return $this->getData('adminConfirm');
    }
}
