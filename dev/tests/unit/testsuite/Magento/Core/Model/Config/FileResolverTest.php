<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Files resolver
     *
     * @var \Magento\Core\Model\Config\FileResolver
     */
    protected $model;

    /**
     * Filesystem
     *
     * @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * File iterator factory
     *
     * @var \Magento\Config\FileIteratorFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $iteratorFactory;

    /**
     * @var \Magento\Module\Dir\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleReader;

    protected function setUp()
    {
        $this->iteratorFactory = $this->getMock(
            'Magento\Config\FileIteratorFactory',
            array(),
            array('getPath'),
            '',
            false
        );
        $this->filesystem = $this->getMock('Magento\Filesystem', array('getDirectoryRead'), array(), '', false);
        $this->moduleReader = $this->getMock(
            'Magento\Module\Dir\Reader',
            array(),
            array('getConfigurationFiles'),
            '',
            false
        );
        $this->model = new \Magento\Core\Model\Config\FileResolver(
            $this->moduleReader,
            $this->filesystem,
            $this->iteratorFactory
        );
    }

    /**
     * Test for get method with primary scope
     *
     * @dataProvider providerGet
     * @param string $filename
     * @param array $fileList
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testGetPrimary($filename, $fileList)
    {
        $scope = 'primary';
        $directory = $this->getMock(
            'Magento\Filesystem\Directory\Read',
            array('search', 'getRelativePath'),
            array(),
            '',
            false
        );
        $directory->expects($this->once())
            ->method('search')
            ->with(sprintf('{%1$s,*/%1$s}', $filename))
            ->will($this->returnValue($fileList));
        $this->filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\Filesystem::CONFIG)
            ->will($this->returnValue($directory));
        $this->iteratorFactory->expects($this->once())
            ->method('create')
            ->with($directory, $fileList)
            ->will($this->returnValue(true));
        $this->assertTrue($this->model->get($filename, $scope));
    }

    /**
     * Test for get method with global scope
     *
     * @dataProvider providerGet
     * @param string $filename
     * @param array $fileList
     */
    public function testGetGlobal($filename, $fileList)
    {
        $scope = 'global';
        $this->moduleReader->expects($this->once())
            ->method('getConfigurationFiles')
            ->with($filename)
            ->will($this->returnValue($fileList));
        $this->assertEquals($fileList, $this->model->get($filename, $scope));
    }

    /**
     * Test for get method with default scope
     *
     * @dataProvider providerGet
     * @param string $filename
     * @param array $fileList
     */
    public function testGetDefault($filename, $fileList)
    {
        $scope = 'some_scope';
        $this->moduleReader->expects($this->once())
            ->method('getConfigurationFiles')
            ->with($scope . '/' . $filename)
            ->will($this->returnValue($fileList));
        $this->assertEquals($fileList, $this->model->get($filename, $scope));
    }

    /**
     * Data provider for get tests
     *
     * @return array
     */
    public function providerGet()
    {
        return array(
            array('di.xml', array('di.xml', 'anotherfolder/di.xml')),
            array('no_files.xml', array()),
            array('one_file.xml', array('one_file.xml'))
        );
    }
}
