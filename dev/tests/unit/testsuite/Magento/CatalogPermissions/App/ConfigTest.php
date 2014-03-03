<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\App;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testIsEnabled()
    {
        $storeConfigMock = $this->getMockForAbstractClass(
            'Magento\Core\Model\Store\ConfigInterface',
            array('getConfigFlag')
        );
        $storeConfigMock->expects($this->once())
            ->method('getConfigFlag')
            ->with(ConfigInterface::XML_PATH_ENABLED, null)
            ->will($this->returnValue('some value'));
        $model = new Config($storeConfigMock);
        $this->assertEquals('some value', $model->isEnabled());
    }
}
