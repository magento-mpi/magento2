<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Backend\Test\Page\Dashboard;
use Magento\Backend\Test\Page\AdminAuthLogin;
use Magento\User\Test\Fixture\AdminUserInjectable;

/**
 * Class LoginUserTest
 * Tests login to backend
 *
 * @package Magento\User\Test\TestCase
 */
class LoginUserTest extends Injectable
{
    /**
     * @var AdminAuthLogin
     */
    protected $loginPage;

    /**
     * @var Dashboard
     */
    protected $dashboard;

    /**
     * @param AdminAuthLogin $loginPage
     * @param Dashboard $dashboard
     */
    public function __inject(AdminAuthLogin $loginPage, Dashboard $dashboard)
    {
        $this->loginPage = $loginPage;
        $this->dashboard = $dashboard;
    }

    /**
     * Log out if the admin user is already logged in.
     */
    protected function setUp()
    {
        $this->dashboard->getAdminPanelHeader()->logOut();
    }

    /**
     * Test admin login to backend
     *
     * @param AdminUserInjectable $adminUser
     */
    public function test(AdminUserInjectable $adminUser)
    {
        // Steps
        $this->loginPage->open();
        $this->loginPage->getLoginBlock()->fillForm($adminUser);
        $this->loginPage->getLoginBlock()->submit();
    }
}
