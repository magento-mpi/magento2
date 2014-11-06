<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module\Updater;

class SetupFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');
    }

    public function testCreateUsesDefaultSetupModelClassIfSetupModelIsNotDeclaredForGivenResource()
    {
        $model = new SetupFactory(
            $this->objectManagerMock,
            array()
        );
        $resource = $this->getMockForAbstractClass('Magento\Framework\Module\ResourceInterface');
        $resourceName = 'module_setup';
        $moduleName = 'module';

        $setupMock = $this->getMockForAbstractClass(
            'Magento\Framework\Module\Updater\SetupInterface',
            array(),
            '',
            true,
            true,
            true,
            array('setResource')
        );
        $setupMock->expects($this->once())->method('setResource')->will($this->returnValue($setupMock));

        $this->objectManagerMock->expects($this->once())->method('create')
            ->with(
                'Magento\Framework\Module\Updater\SetupInterface',
                array(
                    'resourceName' => $resourceName,
                    'moduleName' => $moduleName,
                )
            )
            ->will($this->returnValue($setupMock));
        $model->create($resourceName, $moduleName, $resource);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage \Not\Valid\Setup\Model is not a \Magento\Framework\Module\Updater\SetupInterface
     */
    public function testCreateThrowsExceptionIfSetupModelIsNotValid()
    {
        $model = new SetupFactory(
            $this->objectManagerMock,
            array(
                'module_setup' => '\Not\Valid\Setup\Model',
            )
        );
        $resource = $this->getMockForAbstractClass('Magento\Framework\Module\ResourceInterface');
        $model->create('module_setup', 'module', $resource);
    }
}
