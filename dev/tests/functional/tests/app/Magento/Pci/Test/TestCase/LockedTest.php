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

class LockedTest extends Functional
{
    public function testLockedAdminUser()
    {
        $password = '123123q';
        $incorrectPassword = 'honey';

        //Create test user and set incorrect password
        $user = Factory::getFixtureFactory()->getMagentoUserAdminUser(array('password' => $password));
        $user->persist();

        //Page
        $loginPage = Factory::getPageFactory()->getAdminAuthLogin();
        //Steps
        $loginPage->open();

        $passwordDataSet = array(
            'incorrect password #1' => $incorrectPassword,
            '1' => $incorrectPassword,
            '2' => $incorrectPassword,
            '3' => $incorrectPassword,
            '4' => $incorrectPassword,
            '5' => $incorrectPassword,
            'correct value' => $password,
        );

        foreach ($passwordDataSet as $currentPassword) {
            $user->setPassword($currentPassword);
            $loginPage->getLoginBlockForm()->fill($user);
            $loginPage->getLoginBlockForm()->submit();
            $expectedErrorMessage = 'Please correct the user name or password.';
            $actualErrorMessage = $loginPage->getMessagesBlock()->getErrorMessages();
            $this->assertEquals($expectedErrorMessage, $actualErrorMessage);
        }
    }
}
