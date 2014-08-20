<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Backend\AdminConfig
 */
namespace Magento\Backend;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test AdminConfig
 *
 * @package Magento\Backend
 */
class AdminConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for setting session name for admin
     *
     */
    public function testSetName()
    {
        $adminConfig = Bootstrap::getObjectManager()->get(
            'Magento\Backend\AdminConfig'
        );
        $this->assertSame(AdminConfig::SESSION_NAME_ADMIN, $adminConfig->getName());
    }
}
