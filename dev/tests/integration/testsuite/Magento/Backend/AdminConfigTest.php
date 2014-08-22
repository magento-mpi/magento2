<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Magento\Backend\AdminConfig
 *
 */
class AdminConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for setting session name for admin
     *
     */
    public function testSetSessionNameByConstructor()
    {
        $sessionName = 'adminHtmlSession';
        $adminConfig = Bootstrap::getObjectManager()->create(
            'Magento\Backend\AdminConfig',
            ['sessionName' => $sessionName]
        );
        $this->assertSame($sessionName, $adminConfig->getName());
    }
}
