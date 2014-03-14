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
     * @var \Magento\View\Asset\Service|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetService;

    /**
     * @var \Magento\View\Publisher|\PHPUnit_Framework_MockObject_MockObject
     */
    private $publisher;

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
        $this->markTestIncomplete('MAGETWO-21654');
        $this->assetService = $this->getMock('Magento\View\Asset\Service', array(), array(), '', false);
        $this->publisher = $this->getMock('Magento\View\Publisher', array(), array(), '', false);
        $this->viewFilesystem = $this->getMock('Magento\View\FileSystem', array(), array(), '', false);
        $this->viewFilesystem->expects($this->any())
            ->method('normalizePath')
            ->will($this->returnArgument(0));
        $this->assetService->expects($this->any())
            ->method('updateDesignParams')
            ->will($this->returnArgument(0));
        $this->object = new \Magento\View\FileResolver(
            $this->assetService,
            $this->publisher,
            $this->viewFilesystem
        );
    }

    /**
     * @param bool $fileOperationsAllowed
     * @param string $manager
     *
     * @dataProvider getViewFileDataProvider
     */
    public function testGetViewFile($fileOperationsAllowed,  $manager)
    {
        $file = 'scope::some/file.js';
        $normalizedFile = 'some/file.js';
        $expectedFile = 'expected/file.js';
        $params = array('param1' => 'param 1', 'param2' => 'param 2');

        $this->assetService->expects($this->once())
            ->method('extractScope')
            ->with($file, $params)
            ->will($this->returnValue($normalizedFile));
        $this->$manager->expects($this->once())
            ->method('getViewFile')
            ->with($normalizedFile, $params)
            ->will($this->returnValue($expectedFile));
        $actualFile = $this->object->getViewFile($file, $params);
        $this->assertSame($expectedFile, $actualFile);
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
}
