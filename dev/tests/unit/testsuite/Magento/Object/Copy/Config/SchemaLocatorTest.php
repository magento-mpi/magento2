<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Object\Copy\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileSystemMock;

    /**
     * @var \Magento\Object\Copy\Config\SchemaLocator
     */
    protected $model;

    protected function setUp()
    {
        $this->fileSystemMock = $this->getMock(
            'Magento\Framework\App\Filesystem',
            array(), array(), '', false
        );
        $this->fileSystemMock->expects($this->any())
            ->method('getPath')->with(\Magento\Framework\App\Filesystem::ROOT_DIR)->will($this->returnValue('schema_dir'));

        $this->model =
            new \Magento\Object\Copy\Config\SchemaLocator($this->fileSystemMock, 'schema.xsd', 'perFileSchema.xsd');
    }

    public function testGetSchema()
    {
        $this->assertEquals('schema_dir/schema.xsd', $this->model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals('schema_dir/perFileSchema.xsd', $this->model->getPerFileSchema());
    }
}
