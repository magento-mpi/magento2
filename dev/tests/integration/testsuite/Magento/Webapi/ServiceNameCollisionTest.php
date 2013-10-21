<?php
namespace Magento\Webapi;

class ServiceNameCollisionTest extends \PHPUnit_Framework_TestCase
{
    public function testServiceNameCollisions()
    {
        $soapConfig = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Webapi\Model\Soap\Config');
        $path = \Magento\TestFramework\Utility\Files::init()->getPathToSource() . '/app/code/Magento';
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST
        );

        $serviceNames = array();

        foreach ($files as $name => $file) {
            if (preg_match('#((?:\w+[/\\\\]){2}Service[/\\\\]\w+V\d+Interface)\.php#', $name, $matches)) {
                $newServiceName = $soapConfig->getServiceName($matches[1]);
                $this->assertFalse(in_array($newServiceName, $serviceNames));
                $serviceNames[] = $newServiceName;
            }
        }
    }
}
