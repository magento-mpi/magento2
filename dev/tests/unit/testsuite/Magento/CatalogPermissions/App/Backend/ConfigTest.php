<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\App\Backend;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testIsEnabled()
    {
        $configMock = $this->getMockForAbstractClass(
            'Magento\Core\Model\ConfigInterface',
            array('getValue')
        );
        $configMock->expects($this->once())
            ->method('getValue')
            ->with(\Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_ENABLED)
            ->will($this->returnValue('some value'));
        $model = new Config($configMock);
        $this->assertEquals('some value', $model->isEnabled());
    }
}
