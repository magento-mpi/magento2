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
use Magento\User\Test\Fixture\User;

/**
 * Class LoginUserTest
 * Tests login to backend
 *
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
     * Setup data for test
     *
     * @param AdminAuthLogin $loginPage
     * @param Dashboard $dashboard
     * @return void
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
     * @param User $user
     * @return void
     */
    public function test(User $user)
    {
        // Steps
        $this->loginPage->open();
        $this->loginPage->getLoginBlock()->fill($user);
        $this->loginPage->getLoginBlock()->submit();
    }
}
