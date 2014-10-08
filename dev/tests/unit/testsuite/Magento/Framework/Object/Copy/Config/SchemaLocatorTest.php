<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Object\Copy\Config;

use Magento\Framework\App\Filesystem\DirectoryList;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileSystemMock;

    /**
     * @var \Magento\Framework\Object\Copy\Config\SchemaLocator
     */
    protected $model;

    protected function setUp()
    {
        $this->fileSystemMock = $this->getMock(
            'Magento\Framework\App\Filesystem',
            array(),
            array(),
            '',
            false
        );
        $this->fileSystemMock->expects($this->any())
            ->method('getPath')
            ->with(DirectoryList::ROOT)
            ->will($this->returnValue('schema_dir'));

        $this->model = new \Magento\Framework\Object\Copy\Config\SchemaLocator(
            $this->fileSystemMock,
            'schema.xsd',
            'perFileSchema.xsd'
        );
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
