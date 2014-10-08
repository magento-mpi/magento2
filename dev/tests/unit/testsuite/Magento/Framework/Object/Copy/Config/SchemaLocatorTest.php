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
     * @var \Magento\Framework\Object\Copy\Config\SchemaLocator
     */
    protected $model;

    protected function setUp()
    {
        $rootDirMock = $this->getMockForAbstractClass('\Magento\Framework\Filesystem\Directory\ReadInterface');
        $rootDirMock->expects($this->exactly(2))
            ->method('getAbsolutePath')
            ->will($this->returnCallback(function ($path) {
                return 'schema_dir/' . $path;
            }));
        $fileSystemMock = $this->getMock(
            'Magento\Framework\Filesystem',
            array(),
            array(),
            '',
            false
        );
        $fileSystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::ROOT)
            ->will($this->returnValue($rootDirMock));

        $this->model = new \Magento\Framework\Object\Copy\Config\SchemaLocator(
            $fileSystemMock,
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
