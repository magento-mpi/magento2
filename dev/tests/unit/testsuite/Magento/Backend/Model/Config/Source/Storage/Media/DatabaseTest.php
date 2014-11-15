<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source\Storage\Media;

use Magento\Framework\App\DeploymentConfig\ResourceConfig;

/**
 * Class DatabaseTest
 */
class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Source\Storage\Media\Database
     */
    protected $mediaDatabase;

    /**
     * @var \Magento\Framework\App\DeploymentConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    protected function setUp()
    {
        $this->configMock = $this->getMock('Magento\Framework\App\DeploymentConfig', array(), array(), '', false);
        $this->configMock->expects(
            $this->any()
        )->method(
            'getSegment'
        )->with(
            ResourceConfig::CONFIG_KEY
        )->will(
            $this->returnValue(
            array('default_setup' => array('name' => 'default_setup', ResourceConfig::KEY_CONNECTION => 'connect1'),
                'custom_resource' => array('name' => 'custom_resource', ResourceConfig::KEY_CONNECTION => 'connect2'),
            )
        )
        );
        $this->mediaDatabase = new \Magento\Backend\Model\Config\Source\Storage\Media\Database($this->configMock);
    }

    /**
     * test to option array
     */
    public function testToOptionArray()
    {
        $this->assertNotEquals(
            $this->mediaDatabase->toOptionArray(),
            array(
                array('value' => 'default_setup', 'label' => 'default_setup'),
                array('value' => 'custom_resource', 'label' => 'custom_resource')
            )
        );

        $this->assertEquals(
            $this->mediaDatabase->toOptionArray(),
            array(
                array('value' => 'custom_resource', 'label' => 'custom_resource'),
                array('value' => 'default_setup', 'label' => 'default_setup')
            )
        );
        $this->assertEquals(
            current($this->mediaDatabase->toOptionArray()),
            array('value' => 'custom_resource', 'label' => 'custom_resource')
        );
    }
}
