<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\ObjectManager;

class DefinitionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemDriverMock;

    /**
     * @var \Magento\ObjectManager\DefinitionFactory
     */
    protected $model;

    /**
     * @var string
     */
    protected $sampleContent;

    protected function setUp()
    {
        $this->sampleContent = serialize(array(1, 2, 3));
        $this->filesystemDriverMock = $this->getMock(
            'Magento\Framework\Filesystem\Driver\File',
            array(),
            array(),
            '',
            false
        );
        $this->model = new \Magento\ObjectManager\DefinitionFactory(
            $this->filesystemDriverMock,
            'DefinitionDir',
            'GenerationDir',
            'serialized'
        );
    }

    public function createClassDefinitionProvider()
    {
        return array(
            'from array' => array(
                null,
                '\Magento\ObjectManager\Definition\Runtime'
            ),
            'from file' => array(
                $this->sampleContent,
                '\Magento\ObjectManager\Definition\Compiled\Serialized'
            ),
        );
    }

    public function testCreateClassDefinitionFromString()
    {
        $this->assertInstanceOf(
            '\Magento\ObjectManager\Definition\Compiled\Serialized',
            $this->model->createClassDefinition($this->sampleContent)
        );
    }

    /**
     * @param string $path
     * @param string $callMethod
     * @param string $expectedClass
     * @dataProvider createPluginsAndRelationsReadableDataProvider
     */
    public function testCreatePluginsAndRelationsReadable($path, $callMethod, $expectedClass)
    {
        $this->filesystemDriverMock->expects($this->once())->method('isReadable')
            ->with($path)
            ->will($this->returnValue(true));
        $this->filesystemDriverMock->expects($this->once())->method('fileGetContents')
            ->with($path)
            ->will($this->returnValue($this->sampleContent));
        $this->assertInstanceOf($expectedClass, $this->model->$callMethod());
    }

    public function createPluginsAndRelationsReadableDataProvider()
    {
        return array(
            'relations' => array(
                'DefinitionDir/relations.php',
                'createRelations',
                '\Magento\ObjectManager\Relations\Compiled'
            ),
            'plugins' => array(
                'DefinitionDir/plugins.php',
                'createPluginDefinition',
                '\Magento\Interception\Definition\Compiled'
            ),
        );
    }

    /**
     * @param string $path
     * @param string $callMethod
     * @param string $expectedClass
     * @dataProvider createPluginsAndRelationsNotReadableDataProvider
     */
    public function testCreatePluginsAndRelationsNotReadable($path, $callMethod, $expectedClass)
    {
        $this->filesystemDriverMock->expects($this->once())->method('isReadable')
            ->with($path)
            ->will($this->returnValue(false));
        $this->assertInstanceOf($expectedClass, $this->model->$callMethod());
    }

    public function createPluginsAndRelationsNotReadableDataProvider()
    {
        return array(
            'relations' => array(
                'DefinitionDir/relations.php',
                'createRelations',
                '\Magento\ObjectManager\Relations\Runtime'
            ),
            'plugins' => array(
                'DefinitionDir/plugins.php',
                'createPluginDefinition',
                '\Magento\Interception\Definition\Runtime'
            ),
        );
    }
}