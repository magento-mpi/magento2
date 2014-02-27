<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class FileFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\View\Publisher\FileFactory */
    protected $fileFactory;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\ObjectManager');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->fileFactory = $this->objectManagerHelper->getObject(
            'Magento\View\Publisher\FileFactory',
            [
                'objectManager' => $this->objectManagerMock
            ]
        );
    }

    /**
     * @param string $filePath
     * @param array $viewParams
     * @param string|null $sourcePath
     * @param string $expectedInstance
     * @dataProvider createDataProvider
     */
    public function testCreate($filePath, $viewParams, $sourcePath, $expectedInstance)
    {
        $fileInstance = $this->getMock($expectedInstance, [], [], '', false);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo($expectedInstance),
                $this->equalTo(
                    [
                        'filePath'   => $filePath,
                        'viewParams' => $viewParams,
                        'sourcePath' => $sourcePath
                    ]
                )
            )
            ->will($this->returnValue($fileInstance));
        $this->assertInstanceOf($expectedInstance, $this->fileFactory->create($filePath, $viewParams, $sourcePath));
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        return [
            'css' => [
                'some\file\path.css',
                ['some', 'view', 'params'],
                'some\source\path',
                'Magento\View\Publisher\CssFile'
            ],
            'other' => [
                'some\file\path.gif',
                ['some', 'other', 'view', 'params'],
                'some\other\source\path',
                'Magento\View\Publisher\File'
            ],
        ];
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage stdClass has to implement the publisher file interface.
     */
    public function testCreateWrongInstance()
    {
        $filePath = 'something';
        $viewParams = ['some', 'array'];
        $fileInstance = new \stdClass();
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('stdClass'),
                $this->equalTo(
                    [
                        'filePath'   => $filePath,
                        'viewParams' => $viewParams,
                        'sourcePath' => null
                    ]
                )
            )
            ->will($this->returnValue($fileInstance));
        $fileFactory = new FileFactory($this->objectManagerMock, 'stdClass');
        $fileFactory->create($filePath, $viewParams);
    }
}
