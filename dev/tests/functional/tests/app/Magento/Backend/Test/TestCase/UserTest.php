<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\TestCase;

use Magento\Backend\Test\Fixture\Admin\SuperAdmin;
use Mtf\Factory\Factory;
use Mtf\TestCase\Injectable;

/**
 * Class UserTest
 * Tests login to backend
 *
 */
class UserTest extends Injectable
{
    public function __inject()
    {
        //
    }

    /**
     * Test admin login to backend
     *
     * @param SuperAdmin $fixture
     */
    public function test(SuperAdmin $fixture)
    {
        //Page
        $loginPage = Factory::getPageFactory()->getAdminAuthLogin();
        //Steps
        $loginPage->open();
        $loginPage->getLoginBlock()->fill($fixture);
        $loginPage->getLoginBlock()->submit();
    }
}
