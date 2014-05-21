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
        $resourceName = 'module_setup';
        $moduleName = 'module';
        $this->objectManagerMock->expects($this->once())->method('create')
            ->with(
                'Magento\Framework\Module\Updater\SetupInterface',
                array(
                    'resourceName' => $resourceName,
                    'moduleName' => $moduleName,
                )
            );
        $model->create($resourceName, $moduleName);
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
        $model->create('module_setup', 'module');
    }
}
