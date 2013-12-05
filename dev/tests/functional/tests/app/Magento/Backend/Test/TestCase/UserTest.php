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

namespace Magento\Backend\Test\TestCase;

use Magento\Backend\Test\Fixture\Admin\SuperAdmin;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class UserTest
 * Tests login to backend
 *
 * @package Magento\Backend\Test\TestCase
 */
class UserTest extends Functional
{
    /**
     * Test admin login to backend
     *
     * @param SuperAdmin $fixture injectable
     */
    public function testLoginUser(SuperAdmin $fixture)
    {
        //Page
        $loginPage = Factory::getPageFactory()->getAdminAuthLogin();
        //Steps
        $loginPage->open();
        $loginPage->getLoginBlock()->fill($fixture);
        $loginPage->getLoginBlock()->submit();
    }
}
