<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Framework\ObjectManager;

class DefinitionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemDriverMock;

    /**
     * @var \Magento\Framework\ObjectManager\DefinitionFactory
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
        $this->model = new \Magento\Framework\ObjectManager\DefinitionFactory(
            $this->filesystemDriverMock,
            'DefinitionDir',
            'GenerationDir',
            'serialized'
        );
    }


    public function testCreateDefinitionsReadsCompiledDefinitions()
    {
        $this->filesystemDriverMock->expects($this->once())->method('isReadable')->will($this->returnValue(false));
        $this->assertInstanceOf(
            '\Magento\Framework\ObjectManager\Definition\Runtime',
            $this->model->createClassDefinition(null, true)
        );
    }

    public function testCreateDefinitionsDoesNotReadCompiledDefinitionsIfUseCompiledIsFalse()
    {
        $this->filesystemDriverMock->expects($this->never())->method('isReadable');
        $this->assertInstanceOf(
            '\Magento\Framework\ObjectManager\Definition\Runtime',
            $this->model->createClassDefinition(null, false)
        );
        $autoloadFunctions = spl_autoload_functions();
        spl_autoload_unregister(array_pop($autoloadFunctions));
    }

    public function testCreateClassDefinitionFromString()
    {
        $this->assertInstanceOf(
            '\Magento\Framework\ObjectManager\Definition\Compiled\Serialized',
            $this->model->createClassDefinition($this->sampleContent)
        );
        $autoloadFunctions = spl_autoload_functions();
        spl_autoload_unregister(array_pop($autoloadFunctions));
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
                '\Magento\Framework\ObjectManager\Relations\Compiled'
            ),
            'plugins' => array(
                'DefinitionDir/plugins.php',
                'createPluginDefinition',
                '\Magento\Framework\Interception\Definition\Compiled'
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
                '\Magento\Framework\ObjectManager\Relations\Runtime'
            ),
            'plugins' => array(
                'DefinitionDir/plugins.php',
                'createPluginDefinition',
                '\Magento\Framework\Interception\Definition\Runtime'
            ),
        );
    }
}
