<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source\Storage\Media;

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
            'getResources'
        )->will(
            $this->returnValue(
                array('default_setup' => array('default_setup'), 'custom_resource' => array('custom_resource'))
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
