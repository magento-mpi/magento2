<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

/**
 * @covers \Magento\Framework\App\ObjectManagerFactory
 */
class ObjectManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Magento\Framework\App\FactoryStub::__construct
     */
    public function testCreateObjectManagerFactoryCouldBeOverridden()
    {
        $rootPath = __DIR__ . '/_files/';
        $factory = new ObjectManagerFactory();
        $factory->create($rootPath, array(), false);
    }
}
