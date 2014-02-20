<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Service|\PHPUnit_Framework_MockObject_MockObject
     */
    private $viewService;

    /**
     * @var \Magento\View\Publisher|\PHPUnit_Framework_MockObject_MockObject
     */
    private $publisher;

    /**
     * @var \Magento\View\DeployedFilesManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $deployedFilesManager;

    /**
     * @var \Magento\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $viewFilesystem;

    /**
     * @var \Magento\View\FileResolver
     */
    private $object;

    protected function setUp()
    {
        $this->viewService = $this->getMock('Magento\View\Service', array(), array(), '', false);
        $this->publisher = $this->getMock('Magento\View\Publisher', array(), array(), '', false);
        $this->deployedFilesManager = $this->getMock('Magento\View\DeployedFilesManager', array(), array(), '', false);
        $this->viewFilesystem = $this->getMock('Magento\View\FileSystem', array(), array(), '', false);
        $this->viewFilesystem->expects($this->any())
            ->method('normalizePath')
            ->will($this->returnArgument(0));
        $this->viewService->expects($this->any())
            ->method('updateDesignParams')
            ->will($this->returnArgument(0));
        $this->object = new \Magento\View\FileResolver(
            $this->viewService,
            $this->publisher,
            $this->deployedFilesManager,
            $this->viewFilesystem
        );
    }

    /**
     * @param bool $fileOperationsAllowed
     * @param string $manager
     *
     * @dataProvider getViewFileDataProvider
     */
    public function testGetPublicViewFile($fileOperationsAllowed,  $manager)
    {
        $this->_testGetFile('getPublicViewFile', $fileOperationsAllowed, $manager);
    }

    /**
     * @param bool $fileOperationsAllowed
     * @param string $manager
     *
     * @dataProvider getViewFileDataProvider
     */
    public function testGetPublicViewFilePath($fileOperationsAllowed,  $manager)
    {
        $this->_testGetFile('getPublicViewFilePath', $fileOperationsAllowed, $manager);
    }

    /**
     * @param bool $fileOperationsAllowed
     * @param string $manager
     *
     * @dataProvider getViewFileDataProvider
     */
    public function testGetViewFile($fileOperationsAllowed,  $manager)
    {
        $this->_testGetFile('getViewFile', $fileOperationsAllowed, $manager);
    }

    /**
     * @return array
     */
    public function getViewFileDataProvider()
    {
        return array(
            'file operations allowed' => array(true, 'publisher'),
            'file operations disallowed' => array(false, 'deployedFilesManager'),
        );
    }

    /**
     * @param string $method
     * @param bool $fileOperationsAllowed
     * @param string $manager
     */
    protected function _testGetFile($method, $fileOperationsAllowed,  $manager)
    {
        $file = 'scope::some/file.js';
        $normalizedFile = 'some/file.js';
        $expectedFile = 'expected/file.js';
        $params = array('param1' => 'param 1', 'param2' => 'param 2');

        $this->viewService->expects($this->once())
            ->method('extractScope')
            ->with($file, $params)
            ->will($this->returnValue($normalizedFile));
        $this->viewService->expects($this->once())
            ->method('isViewFileOperationAllowed')
            ->will($this->returnValue($fileOperationsAllowed));
        $this->$manager->expects($this->once())
            ->method($method)
            ->with($normalizedFile, $params)
            ->will($this->returnValue($expectedFile));
        $actualFile = $this->object->$method($file, $params);
        $this->assertSame($expectedFile, $actualFile);
    }
}
