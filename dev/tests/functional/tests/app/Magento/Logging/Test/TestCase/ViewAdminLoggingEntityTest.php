<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Logging\Test\TestCase;

use Magento\Backend\Test\Page\Adminhtml\SystemConfig;
use Magento\Logging\Test\Fixture\Logging;
use Magento\Logging\Test\Page\Adminhtml\Logging as LoggingIndex;
use Mtf\TestCase\Injectable;

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
     * Page SystemConfig
     *
     * @var SystemConfig
     */
    protected $systemConfig;

    /**
     * Page LoggingIndex
     *
     * @var LoggingIndex
     */
    protected $loggingIndex;

    /**
     * Injection data
     *
     * @param SystemConfig $systemConfig
     * @param LoggingIndex $loggingIndex
     * @return void
     */
    public function __inject(SystemConfig $systemConfig, LoggingIndex $loggingIndex)
    {
        $this->systemConfig = $systemConfig;
        $this->loggingIndex = $loggingIndex;
    }

    /**
     * View Admin Logging details on backend
     *
     * @param Logging $logging
     * @return void
     */
    public function testViewAdminLogging(Logging $logging)
    {
        $filter = [
            'username' => $logging->getUser(),
        ];
        //Steps
        $this->systemConfig->open();
        $this->systemConfig->getPageActions()->save();
        $this->loggingIndex->open();
        $this->loggingIndex->getLogGridBlock()->searchSortAndOpen($filter);
    }
}
