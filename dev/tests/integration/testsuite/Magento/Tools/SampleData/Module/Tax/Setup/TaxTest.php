<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Tax\Setup;

use Magento\Tools\SampleData\TestLogger;

/**
 * Class TaxTest
 */
class TaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRun()
    {
        /** @var \Magento\Tools\SampleData\Module\Tax\Setup\Tax $tax */
        $tax = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Tax\Setup\Tax',
            ['logger' => TestLogger::factory()]
        );

        ob_start();
        $tax->run();
        $result = ob_get_clean();
        $this->assertContains('Installing taxes', $result);
        $this->assertContains('..', $result);
    }
}
