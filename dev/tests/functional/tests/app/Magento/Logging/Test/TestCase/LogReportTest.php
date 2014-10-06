<?php
/**
 * {license_notice}
 *
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

        //Step: Login first user
        $loginPage->open();
        $loginPage->getLoginBlock()->fill($configUser);
        $loginPage->getLoginBlock()->submit();
        $loginPage->waitForHeaderBlock();

        //Step: Save config
        $systemConfigPage->open();
        $systemConfigPage->getPageActions()->save();
        $systemConfigPage->getMessagesBlock()->waitSuccessMessage();

        //Step: Logout
        Factory::getApp()->magentoBackendLogoutUser();

        //Step: Login second user
        $loginPage->open();
        $loginPage->getLoginBlock()->fill($loginUser);
        $loginPage->getLoginBlock()->submit();
        $loginPage->waitForHeaderBlock();

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
            'fullActionName' => 'adminhtml_system_config_save',
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
