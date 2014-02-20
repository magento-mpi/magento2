<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Import;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Css\PreProcessor\Cache\Import\Cache */
    protected $cache;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Css\PreProcessor\Cache\Import\Map\Storage|\PHPUnit_Framework_MockObject_MockObject */
    protected $storageMock;

    /** @var \Magento\Css\PreProcessor\Cache\Import\ImportEntityFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $importEntityFactoryMock;

    protected function setUp()
    {
        $this->storageMock = $this->getMock('Magento\Css\PreProcessor\Cache\Import\Map\Storage', [], [], '', false);
        $this->importEntityFactoryMock = $this->getMock(
            'Magento\Css\PreProcessor\Cache\Import\ImportEntityFactory',
            [],
            [],
            '',
            false
        );

        $cssFile = $this->getMock('Magento\View\Publisher\CssFile', [], [], '', false);

        $cssFile->expects($this->once())
            ->method('getFilePath')
            ->will($this->returnValue('Magento_Core::style.css'));

        $cssFile->expects($this->once())
            ->method('getViewParams')
            ->will($this->returnValue(['theme' => 'some_theme', 'area' => 'frontend', 'locale' => 'en_US']));

        $fileFactory = $this->getMock(
            'Magento\View\Publisher\FileFactory',
            [],
            [],
            '',
            false
        );

        $fileFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($cssFile));

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->cache = $this->objectManagerHelper->getObject(
            'Magento\Css\PreProcessor\Cache\Import\Cache',
            [
                'storage' => $this->storageMock,
                'importEntityFactory' => $this->importEntityFactoryMock,
                'fileFactory' => $fileFactory,
                'publisherFile' => $cssFile
            ]
        );
    }

    public function testClearCache()
    {
        $expectedKey = 'Magento_Core::style.css|frontend|en_US|some_theme';

        $fileKeyProperty = new \ReflectionProperty($this->cache, 'uniqueFileKey');
        $fileKeyProperty->setAccessible(true);
        $this->assertEquals($expectedKey, $fileKeyProperty->getValue($this->cache));

        $cachedFileProperty = new \ReflectionProperty($this->cache, 'cachedFile');
        $cachedFileProperty->setAccessible(true);
        $cachedFileProperty->setValue($this->cache, 'some_cachedFile');

        $importEntitiesProperty = new \ReflectionProperty($this->cache, 'importEntities');
        $importEntitiesProperty->setAccessible(true);
        $this->assertEquals([], $importEntitiesProperty->getValue($this->cache));
        $importEntitiesProperty->setValue($this->cache, ['some_import_1', 'some_import_2']);

        $this->storageMock->expects($this->once())
            ->method('delete')
            ->with($this->equalTo($expectedKey))
            ->will($this->returnSelf());

        $this->assertEquals($this->cache, $this->cache->clear());
        $this->assertEmpty($cachedFileProperty->getValue($this->cache));
        $this->assertEquals([], $importEntitiesProperty->getValue($this->cache));
    }

    public function testGetCachedFile()
    {
        $property = new \ReflectionProperty($this->cache, 'cachedFile');
        $property->setAccessible(true);
        $this->assertEmpty($property->getValue($this->cache));
        $property->setValue(
            $this->cache,
            $this->getMock('Magento\View\Publisher\CssFile', [], [], '', false)
        );
        $this->assertInstanceOf('\Magento\View\Publisher\CssFile', $this->cache->get());
    }

    /**
     * @param array $params
     * @param array $expectedResult
     * @dataProvider addEntityToCacheDataProvider
     */
    public function testAddEntityToCache($params, $expectedResult)
    {
        $importEntitiesProperty = new \ReflectionProperty($this->cache, 'importEntities');
        $importEntitiesProperty->setAccessible(true);
        $this->assertEquals([], $importEntitiesProperty->getValue($this->cache));

        $this->importEntityFactoryMock
            ->expects($this->any())
            ->method('create')
            ->with($this->isInstanceOf('Magento\Less\PreProcessor\File\Less'))
            ->will($this->returnValue('entity_object_here'));

        foreach ($params as $value) {
            $this->assertEquals(
                $this->cache,
                $this->cache->add($value)
            );
        }
        $this->assertEquals($expectedResult, $importEntitiesProperty->getValue($this->cache));
    }

    /**
     * @return array
     */
    public function addEntityToCacheDataProvider()
    {
        $themeModelMockId = $this->getMock('Magento\Core\Model\Theme', [], [], '', false);
        $themeModelMockId->expects($this->once())->method('getId')->will($this->returnValue('1'));

        $themeModelMockPath = $this->getMock('Magento\Core\Model\Theme', [], [], '', false);
        $themeModelMockPath->expects($this->once())->method('getThemePath')->will($this->returnValue('mocked_path'));
        return [
            'one import' => [
                'params' => [
                    $this->getLessFile(
                        'css\some_file.css',
                        ['theme' => 'other_theme', 'area' => 'backend', 'locale' => 'fr_FR']
                    )
                ],
                'expectedResult' => ['css\some_file.css|backend|fr_FR|other_theme' => 'entity_object_here']
            ],
            'one import with theme id' => [
                'params' => [
                    $this->getLessFile(
                        'css\theme_id\some_file.css',
                        ['themeModel' => $themeModelMockId, 'area' => 'backend', 'locale' => 'en_En']
                    )
                ],
                'expectedResult' => ['css\theme_id\some_file.css|backend|en_En|1' => 'entity_object_here']
            ],
            'one import with theme path' => [
                'params' => [
                    $this->getLessFile(
                        'css\some_file.css',
                        ['themeModel' => $themeModelMockPath, 'area' => 'frontend']
                    )
                ],
                'expectedResult' => ['css\some_file.css|frontend|088d309371332feb12bad4dbf93cfb5d'
                    => 'entity_object_here']
            ],
            'list of imports' => [
                'params' => [
                    $this->getLessFile(
                        'Magento_Core::folder\file.css',
                        ['theme' => 'theme_path', 'area' => 'backend']
                    ),
                    $this->getLessFile(
                        'calendar\button.css',
                        ['theme' => 'theme_path', 'area' => 'backend', 'locale' => 'en_US']
                    )
                ],
                'expectedResult' => [
                    'Magento_Core::folder\file.css|backend|theme_path' => 'entity_object_here',
                    'calendar\button.css|backend|en_US|theme_path' => 'entity_object_here',
                ]
            ],
        ];
    }

    /**
     * @param string $filePath
     * @param array $viewParams
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getLessFile($filePath, $viewParams)
    {
        $lessFile = $this->getMock('Magento\Less\PreProcessor\File\Less', [], [], '', false);

        $lessFile->expects($this->any())
            ->method('getFilePath')
            ->will($this->returnValue($filePath));

        $lessFile->expects($this->any())
            ->method('getViewParams')
            ->will($this->returnValue($viewParams));

        return $lessFile;
    }

    /**
     * @param \Magento\View\Publisher\CssFile $cssFile
     * @param string $uniqueFileKey
     * @param array $expected
     * @dataProvider saveCacheDataProvider
     */
    public function testSaveCache($cssFile, $uniqueFileKey, $expected)
    {
        $importEntitiesProperty = new \ReflectionProperty($this->cache, 'importEntities');
        $importEntitiesProperty->setAccessible(true);
        $this->assertEquals([], $importEntitiesProperty->getValue($this->cache));
        $importEntitiesProperty->setValue($this->cache, $expected['imports']);

        $this->storageMock->expects($this->once())
            ->method('save')
            ->with($this->equalTo($uniqueFileKey), $this->equalTo(serialize($expected)))
            ->will($this->returnSelf());
        $this->assertEquals($this->cache, $this->cache->save($cssFile));
    }

    /**
     * @return array
     */
    public function saveCacheDataProvider()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = $objectManager->getConstructArguments(
            'Magento\View\Publisher\CssFile',
            ['viewParams' => ['area' => 'frontend']]
        );

        $cssFile = $objectManager->getObject('Magento\View\Publisher\CssFile', $arguments);

        return [
            [
                $cssFile,
                'Magento_Core::style.css|frontend|en_US|some_theme',
                [
                    'cached_file' => $cssFile,
                    'imports' => ['import1', 'import2', 'import3']
                ]
            ]
        ];
    }

    /**
     * @param ImportEntity[] $importData
     * @param bool $expected
     * @dataProvider isValidDataProvider
     */
    public function testIsValid($importData, $expected)
    {
        $importEntitiesProperty = new \ReflectionProperty($this->cache, 'importEntities');
        $importEntitiesProperty->setAccessible(true);
        $this->assertEquals([], $importEntitiesProperty->getValue($this->cache));
        $importEntitiesProperty->setValue($this->cache, $importData);

        $method = new \ReflectionMethod('Magento\Css\PreProcessor\Cache\Import\Cache', 'isValid');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invoke($this->cache));
    }

    /**
     * @return array
     */
    public function isValidDataProvider()
    {
        $importEntityTrue = $this->getMock('Magento\Css\PreProcessor\Cache\Import\ImportEntity', [], [], '', false);
        $importEntityTrue->expects($this->once())->method('isValid')->will($this->returnValue(true));

        $importEntityFalse = $this->getMock('Magento\Css\PreProcessor\Cache\Import\ImportEntity', [], [], '', false);
        $importEntityFalse->expects($this->once())->method('isValid')->will($this->returnValue(false));
        return [
            [[$importEntityTrue], true],
            [[$importEntityFalse], false],
            [[], false]
        ];
    }
}
