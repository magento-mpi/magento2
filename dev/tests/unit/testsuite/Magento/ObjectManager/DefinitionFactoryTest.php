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
    protected $fsDriverMock;

    /**
     * @var \Magento\ObjectManager\DefinitionFactory
     */
    protected $model;

    /**
     * @var string
     */
    protected $sampleContent = 'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}';

    protected function setUp()
    {
        $this->fsDriverMock = $this->getMock('Magento\Filesystem\Driver\File', array(), array(), '', false);
        $this->model = new \Magento\ObjectManager\DefinitionFactory(
            $this->fsDriverMock,
            'DefinitionDir',
            'GenerationDir',
            'serialized'
        );
    }

    /**
     * @param string $fileContent
     * @param string $expectedModel
     * @dataProvider createClassDefinitionProvider
     */
    public function testCreateClassDefinitionFromArrayAndFile($fileContent, $expectedModel)
    {
        $this->fsDriverMock->expects($this->once())->method('isReadable')->will($this->returnValue(true));
        $this->fsDriverMock->expects($this->once())->method('fileGetContents')
            ->will($this->returnValue($fileContent));
        $this->assertInstanceOf($expectedModel, $this->model->createClassDefinition(null));
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
     * @dataProvider createPluginsAndRelationsReadableProvider
     */
    public function testCreatePluginsAndRelationsReadable($path, $callMethod, $expectedClass)
    {
        $this->fsDriverMock->expects($this->once())->method('isReadable')
            ->with($path)
            ->will($this->returnValue(true));
        $this->fsDriverMock->expects($this->once())->method('fileGetContents')
            ->with($path)
            ->will($this->returnValue($this->sampleContent));
        $this->assertInstanceOf($expectedClass, $this->model->$callMethod());
    }

    public function createPluginsAndRelationsReadableProvider()
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
     * @dataProvider createPluginsAndRelationsNotReadableProvider
     */
    public function testCreateRelationsNotReadable($path, $callMethod, $expectedClass)
    {
        $this->fsDriverMock->expects($this->once())->method('isReadable')
            ->with($path)
            ->will($this->returnValue(false));
        $this->assertInstanceOf($expectedClass, $this->model->$callMethod());
    }

    public function createPluginsAndRelationsNotReadableProvider()
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