<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    protected $apacheRewrites = [
        'attribute_code' => 'apacheRewrites',
        'backend_type' => 'virtual',
    ];

    protected $dbTablePrefix = [
        'attribute_code' => 'dbTablePrefix',
        'backend_type' => 'virtual',
    ];

    protected $keyOwn = [
        'attribute_code' => 'keyOwn',
        'backend_type' => 'virtual',
    ];

    protected $httpsAdmin = [
        'attribute_code' => 'httpsAdmin',
        'backend_type' => 'virtual',
    ];

    protected $httpsFront = [
        'attribute_code' => 'httpsFront',
        'backend_type' => 'virtual',
    ];

    protected $keyValue = [
        'attribute_code' => 'keyValue',
        'backend_type' => 'virtual',
    ];

    protected $language = [
        'attribute_code' => 'language',
        'backend_type' => 'virtual',
    ];

    protected $currency = [
        'attribute_code' => 'language',
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

    public function getCurrency()
    {
        return $this->getData('currency');
    }

    public function getApacheRewrites()
    {
        return $this->getData('apacheRewrites');
    }

    public function getKeyOwn()
    {
        return $this->getData('keyOwn');
    }

    public function getKeyValue()
    {
        return $this->getData('keyValue');
    }

    public function getLanguage()
    {
        return $this->getData('language');
    }

    public function getHttpsAdmin()
    {
        return $this->getData('httpsAdmin');
    }

    public function getHttpsFront()
    {
        return $this->getData('httpsFront');
    }

    public function getDbTablePrefix()
    {
        return $this->getData('dbTablePrefix');
    }
}
