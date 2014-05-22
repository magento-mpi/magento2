<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Test\TestCase;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Magento\Backend\Test\Page\Adminhtml\SystemConfig;
use Magento\Logging\Test\Page\Adminhtml\Logging;
use Magento\User\Test\Fixture\AdminUserInjectable;

/**
 * Test Creation for ViewAdminLoggingEntity
 *
 * Test Flow:
 * 1. Log in as admin user
 * 2. Go to Stores>Configuration
 * 3. Click “Save Config”  button
 * 4. Go to System>Action Log>Report
 * 5. Filter by logged user name
 * 6. Click on top action
 * 7. Perform all assertion
 *
 * @group Admin_Logging_(MX)
 * @ZephyrId MAGETWO-24702
 */
class ViewAdminLoggingEntityTest extends Injectable
{
    /**
     * @var SystemConfig
     */
    protected $systemConfig;

    /**
     * @var Logging
     */
    protected $logging;

    /**
     * @param SystemConfig $systemConfig
     * @param Logging $logging
     */
    public function __inject(SystemConfig $systemConfig, Logging $logging)
    {
        $this->systemConfig = $systemConfig;
        $this->logging = $logging;
    }

    /**
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $adminUser = $fixtureFactory->create('Magento\User\Test\Fixture\AdminUserInjectable', ['dataSet' => 'default']);
        return ['adminUser' => $adminUser];
    }

    /**
     * View Admin Logging details on backend
     *
     * @param AdminUserInjectable $adminUser
     */
    public function testViewAdminLogging(AdminUserInjectable $adminUser)
    {
        $filter = [
            'username' => $adminUser->getUsername(),
        ];
        //Steps
        $this->systemConfig->open();
        $this->systemConfig->getPageActions()->save();
        $this->logging->open();
        $this->logging->getLogGrid()->search($filter);
        $this->logging->getLogGrid()->clickViewLink();
    }
}
