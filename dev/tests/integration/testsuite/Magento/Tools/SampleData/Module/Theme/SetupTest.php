<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Theme;

/**
 * Class ThemeTest
 */
class SetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRun()
    {
        /** @var \Magento\Tools\SampleData\Module\Theme\Setup $model */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Theme\Setup'
        );

        ob_start();
        $model->run();
        $result = ob_get_clean();
        $this->assertContains('Installing theme', $result);
        $this->assertContains('.', $result);
    }
}
