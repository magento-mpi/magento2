<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Tools\SampleData\Module\Cms\Setup;

use Magento\Tools\SampleData\TestLogger;

/**
 * Class PageTest
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRun()
    {
        /** @var \Magento\Tools\SampleData\Module\Cms\Setup\Page $model */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Cms\Setup\Page',
            ['logger' => TestLogger::factory()]
        );

        ob_start();
        $model->run();
        $result = ob_get_clean();
        $this->assertContains('Installing CMS pages', $result);
        $this->assertContains('....', $result);
    }
}
