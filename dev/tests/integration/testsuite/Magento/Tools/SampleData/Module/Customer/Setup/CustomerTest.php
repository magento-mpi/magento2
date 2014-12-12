<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Tools\SampleData\Module\Customer\Setup;

use Magento\Tools\SampleData\TestLogger;

/**
 * Class CustomerTest
 */
class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRun()
    {
        /** @var \Magento\Tools\SampleData\Module\Customer\Setup\Customer $customer */
        $customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Customer\Setup\Customer',
            ['logger' => TestLogger::factory()]
        );

        ob_start();
        $customer->run();
        $result = ob_get_clean();
        $this->assertContains('Installing customers', $result);
        $this->assertContains('.', $result);
    }
}
