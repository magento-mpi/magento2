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

namespace Magento\Logging\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class LogReportTest
 * Tests admin user action are logged and available in actions report
 *
 * @package Magento\Logging\Test\TestCase
 */
class LogReportTest extends Functional
{
    /**
     * Test admin backend manipulations are logged
     *
     * @ZephyrId MAGETWO-12411
     */
    public function testConfigActionsLogged()
    {
        //Pre-conditions: two admin users creation
        $configUser = Factory::getFixtureFactory()->getMagentoUserAdminUser();
        $configUser->switchData('admin_default');
        $configUser->persist();

        $loginUser = Factory::getFixtureFactory()->getMagentoUserAdminUser();
        $loginUser->switchData('admin_default');
        $loginUser->persist();

        //Pages
        $loginPage = Factory::getPageFactory()->getAdminAuthLogin();
        $systemConfigPage = Factory::getPageFactory()->getAdminSystemConfig();
        $logReportPage = Factory::getPageFactory()->getAdminLogging();

        //Blocks
        $configForm = $systemConfigPage->getForm();

        //Step: Login first user
        $loginPage->open();
        $loginPage->getLoginBlock()->fill($configUser);
        $loginPage->getLoginBlock()->submit();

        //Step: Save config
        $systemConfigPage->open();
        $configForm->save();
        $systemConfigPage->getMessagesBlock()->assertSuccessMessage();

        //Step: Logout
        Factory::getApp()->magentoBackendLogoutUser();

        //Step: Login second user
        $loginPage->open();
        $loginPage->getLoginBlock()->fill($loginUser);
        $loginPage->getLoginBlock()->submit();

        //Step: Open logging report grid
        $logReportPage->open();

        //Verification: Login action present
        $loginActionLog = array(
            'username' => $configUser->getUsername(),
            'actionGroup' => 'Admin Sign In',
            'action' => 'Login',
            'result' => 'Success',
            'fullActionName' => 'adminhtml_auth_login',
        );
        $logReportPage->getLogGridBlock()->isRowVisible($loginActionLog);

        //Verification: Config view action present
        $configActionLog = array(
            'username' => $configUser->getUsername(),
            'actionGroup' => 'System Configuration',
            'action' => 'View',
            'result' => 'Success',
            'fullActionName' => 'adminhtml_system_config_edit',
        );
        $logReportPage->getLogGridBlock()->isRowVisible($configActionLog);

        //Verification: Config save action present
        //Because of this verification test is incomplete
        $configActionLog = array(
            'username' => $configUser->getUsername(),
            'actionGroup' => 'System Configuration',
            'action' => 'Save',
            'result' => 'Success',
            'fullActionName' => 'adminhtml_system_config_save_index',
        );
        $logReportPage->getLogGridBlock()->isRowVisible($configActionLog);

        //Verification: Second user login action present
        $loginActionLog = array(
            'username' => $loginUser->getUsername(),
            'actionGroup' => 'Admin Sign In',
            'action' => 'Login',
            'result' => 'Success',
            'fullActionName' => 'adminhtml_auth_login',
        );
        $logReportPage->getLogGridBlock()->isRowVisible($loginActionLog);
    }
}
