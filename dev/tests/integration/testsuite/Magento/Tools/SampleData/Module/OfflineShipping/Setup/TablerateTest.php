<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData\Module\OfflineShipping\Setup;


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
            'Magento\Tools\SampleData\Module\OfflineShipping\Setup\Tablerate'
        );

        ob_start();
        $rate->run();
        $result = ob_get_clean();
        $this->assertContains('Installing Tablerate', $result);
        $this->assertContains('.........', $result);
    }
}
