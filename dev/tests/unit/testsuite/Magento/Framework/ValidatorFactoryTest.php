<?php
/**
 * Unit test for Magento\Framework\ValidatorFactory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework;

use Magento\TestFramework\Helper\ObjectManager;

class ValidatorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Framework\ValidatorFactory */
    private $model;

    /** @var \Magento\Framework\ObjectManager | \PHPUnit_Framework_MockObject_MockObject */
    private $objectManagerMock;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManager->getObject('Magento\Framework\ValidatorFactory',
            ['objectManager' => $this->objectManagerMock]
        );
    }

    public function testCreateWithInstanceName()
    {
        $setName = 'Magento\Framework\Object';
        $returnMock = $this->getMock($setName);
        $this->objectManagerMock->expects($this->once())->method('create')
            ->willReturn($returnMock);

        $this->model->setInstanceName($setName);
        $this->assertSame($returnMock, $this->model->create());
    }

    public function testCreateDefault()
    {
        $default = 'Magento\Framework\Validator';
        $returnMock = $this->getMock($default);
        $this->objectManagerMock->expects($this->once())->method('create')
            ->willReturn($returnMock);

        $this->model->setInstanceName($default);
        $this->assertSame($returnMock, $this->model->create());
    }
}