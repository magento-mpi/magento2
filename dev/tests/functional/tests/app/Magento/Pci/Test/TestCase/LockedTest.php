<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pci\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class LockedTest
 * Functional test for locked admin user
 *
 */
class LockedTest extends Functional
{
    /**
     * Preventing locked Admin user to log in into the backend
     *
     * @ZephyrId MAGETWO-12386
     */
    public function testLockedAdminUser()
    {
        //Data
        $password = '123123q';
        $incorrectPassword = 'honey boo boo';
        $passwordDataSet = array(
            'incorrect password #1' => $incorrectPassword,
            'incorrect password #2' => $incorrectPassword,
            'incorrect password #3' => $incorrectPassword,
            'incorrect password #4' => $incorrectPassword,
            'incorrect password #5' => $incorrectPassword,
            'incorrect password #6' => $incorrectPassword,
            'correct password' => $password,
        );
        //Create test user and set correct password
        $user = Factory::getFixtureFactory()->getMagentoUserAdminUser(array('password' => $password));
        $user->switchData('admin_default');
        $user->persist();
        //Page
        $loginPage = Factory::getPageFactory()->getAdminAuthLogin();
        //Steps
        $loginPage->open();
        $expectedErrorMessage = 'Please correct the user name or password.';
        foreach ($passwordDataSet as $currentPassword) {
            $user->setPassword($currentPassword);
            $loginPage->getLoginBlock()->fill($user);
            $loginPage->getLoginBlock()->submit();
            $actualErrorMessage = $loginPage->getMessagesBlock()->getErrorMessages();
            //Verifying
            $this->assertEquals($expectedErrorMessage, $actualErrorMessage);
        }
    }
}
