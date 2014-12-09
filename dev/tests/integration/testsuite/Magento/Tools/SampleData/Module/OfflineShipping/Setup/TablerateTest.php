<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\OfflineShipping\Setup;

use Magento\Tools\SampleData\TestLoogger;

/**
 * Class TablerateTest
 */
class TablerateTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRun()
    {
        /** @var \Magento\Tools\SampleData\Module\OfflineShipping\Setup\Tablerate $rate */
        $rate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\OfflineShipping\Setup\Tablerate',
            ['logger' => TestLoogger::factory()]
        );

        ob_start();
        $rate->run();
        $result = ob_get_clean();
        $this->assertContains('Installing Tablerate', $result);
        $this->assertContains('.........', $result);
    }
}
