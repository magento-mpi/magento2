<?php
/**
 * Integration test for Magento\Backend\Model\Config\Backend\Cookie\Lifetime
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend\Cookie;

class LifetimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Method is not publicly accessible, so it must be called through parent
     *
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Invalid cookie lifetime: must be numeric
     */
    public function testBeforeSaveException()
    {
        $invalidCookieLifetime = 'invalid lifetime';
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Backend\Model\Config\Backend\Cookie\Lifetime $model */
        $model = $objectManager->create('Magento\Backend\Model\Config\Backend\Cookie\Lifetime');
        $model->setValue($invalidCookieLifetime);
        $model->save();
    }
} 