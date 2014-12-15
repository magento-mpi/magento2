<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Test theme model
 */
namespace Magento\Core\Model;

use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Design\Theme\Image\PathInterface;

class ThemeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Theme|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_imageFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rootDirMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mediaDirMock;

    protected function setUp()
    {
        $customizationConfig = $this->getMock('Magento\Theme\Model\Config\Customization', [], [], '', false);
        $customizationFactory = $this->getMock(
            'Magento\Framework\View\Design\Theme\CustomizationFactory',
            ['create'],
            [],
            '',
            false
        );
        $resourceCollection = $this->getMock(
            'Magento\Core\Model\Resource\Theme\Collection',
            [],
            [],
            '',
            false
        );
        $this->_imageFactory = $this->getMock(
            'Magento\Framework\View\Design\Theme\ImageFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->filesystemMock = $this->getMockBuilder('Magento\Framework\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rootDirMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\ReadInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mediaDirMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\ReadInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $map = [
            [DirectoryList::ROOT, $this->rootDirMock],
            [DirectoryList::MEDIA, $this->mediaDirMock],
        ];
        $this->filesystemMock->expects($this->exactly(2))
            ->method('getDirectoryRead')
            ->willReturnMap($map);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Magento\Core\Model\Theme',
            [
                'customizationFactory' => $customizationFactory,
                'customizationConfig' => $customizationConfig,
                'imageFactory' => $this->_imageFactory,
                'resourceCollection' => $resourceCollection,
                'filesystem' => $this->filesystemMock,
            ]
        );

        $this->_model = $objectManagerHelper->getObject('Magento\Core\Model\Theme', $arguments);
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @covers \Magento\Core\Model\Theme::getThemeImage
     */
    public function testThemeImageGetter()
    {
        $this->_imageFactory->expects($this->once())->method('create')->with(['theme' => $this->_model]);
        $this->_model->getThemeImage();
    }

    /**
     * @dataProvider isVirtualDataProvider
     * @param int $type
     * @param string $isVirtual
     * @covers \Magento\Core\Model\Theme::isVirtual
     */
    public function testIsVirtual($type, $isVirtual)
    {
        /** @var $themeModel \Magento\Core\Model\Theme */
        $themeModel = $this->getMock('Magento\Core\Model\Theme', ['__wakeup'], [], '', false);
        $themeModel->setType($type);
        $this->assertEquals($isVirtual, $themeModel->isVirtual());
    }

    /**
     * @return array
     */
    public function isVirtualDataProvider()
    {
        return [
            ['type' => ThemeInterface::TYPE_VIRTUAL, 'isVirtual' => true],
            ['type' => ThemeInterface::TYPE_STAGING, 'isVirtual' => false],
            ['type' => ThemeInterface::TYPE_PHYSICAL, 'isVirtual' => false]
        ];
    }

    /**
     * @dataProvider isPhysicalDataProvider
     * @param int $type
     * @param string $isPhysical
     * @covers \Magento\Core\Model\Theme::isPhysical
     */
    public function testIsPhysical($type, $isPhysical)
    {
        /** @var $themeModel \Magento\Core\Model\Theme */
        $themeModel = $this->getMock('Magento\Core\Model\Theme', ['__wakeup'], [], '', false);
        $themeModel->setType($type);
        $this->assertEquals($isPhysical, $themeModel->isPhysical());
    }

    /**
     * @return array
     */
    public function isPhysicalDataProvider()
    {
        return [
            ['type' => ThemeInterface::TYPE_VIRTUAL, 'isPhysical' => false],
            ['type' => ThemeInterface::TYPE_STAGING, 'isPhysical' => false],
            ['type' => ThemeInterface::TYPE_PHYSICAL, 'isPhysical' => true]
        ];
    }

    /**
     * @dataProvider isVisibleDataProvider
     * @param int $type
     * @param string $isVisible
     * @covers \Magento\Core\Model\Theme::isVisible
     */
    public function testIsVisible($type, $isVisible)
    {
        /** @var $themeModel \Magento\Core\Model\Theme */
        $themeModel = $this->getMock('Magento\Core\Model\Theme', ['__wakeup'], [], '', false);
        $themeModel->setType($type);
        $this->assertEquals($isVisible, $themeModel->isVisible());
    }

    /**
     * @return array
     */
    public function isVisibleDataProvider()
    {
        return [
            ['type' => ThemeInterface::TYPE_VIRTUAL, 'isVisible' => true],
            ['type' => ThemeInterface::TYPE_STAGING, 'isVisible' => false],
            ['type' => ThemeInterface::TYPE_PHYSICAL, 'isVisible' => true]
        ];
    }

    /**
     * Test id deletable
     *
     * @dataProvider isDeletableDataProvider
     * @param string $themeType
     * @param bool $isDeletable
     * @covers \Magento\Core\Model\Theme::isDeletable
     */
    public function testIsDeletable($themeType, $isDeletable)
    {
        /** @var $themeModel \Magento\Core\Model\Theme */
        $themeModel = $this->getMock('Magento\Core\Model\Theme', ['getType', '__wakeup'], [], '', false);
        $themeModel->expects($this->once())->method('getType')->will($this->returnValue($themeType));
        $this->assertEquals($isDeletable, $themeModel->isDeletable());
    }

    /**
     * @return array
     */
    public function isDeletableDataProvider()
    {
        return [
            [ThemeInterface::TYPE_VIRTUAL, true],
            [ThemeInterface::TYPE_STAGING, true],
            [ThemeInterface::TYPE_PHYSICAL, false]
        ];
    }

    /**
     * @param mixed $originalCode
     * @param string $expectedCode
     * @dataProvider getCodeDataProvider
     */
    public function testGetCode($originalCode, $expectedCode)
    {
        $this->_model->setCode($originalCode);
        $this->assertSame($expectedCode, $this->_model->getCode());
    }

    /**
     * @return array
     */
    public function getCodeDataProvider()
    {
        return [
            'string code' => ['theme/code', 'theme/code'],
            'null code' => [null, ''],
            'number code' => [10, '10']
        ];
    }

    public function testGetPreviewImagePath()
    {
        $previewImage = 'test.jpg';
        $expectedPath = 'pub/media/theme/preview/' . $previewImage;
        $absolutePath = '/var/www/html' . $expectedPath;

        $this->mediaDirMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with(PathInterface::PREVIEW_DIRECTORY_PATH . '/' . $previewImage)
            ->willReturn($absolutePath);
        $this->rootDirMock->expects($this->once())
            ->method('getRelativePath')
            ->with($absolutePath)
            ->willReturn($expectedPath);

        $this->_model->setPreviewImage($previewImage);
        $result = $this->_model->getPreviewImagePath();
        $this->assertEquals($expectedPath, $result);
    }
}
